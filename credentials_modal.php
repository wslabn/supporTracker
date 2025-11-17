<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

$asset_id = $_GET['asset_id'] ?? null;
if (!$asset_id) {
    redirect('assets.php');
}

// Get asset info
$stmt = $pdo->prepare("
    SELECT a.*, c.name as company_name, e.name as employee_name 
    FROM assets a 
    JOIN companies c ON a.company_id = c.id 
    LEFT JOIN employees e ON a.employee_id = e.id 
    WHERE a.id = ?
");
$stmt->execute([$asset_id]);
$asset = $stmt->fetch();

if (!$asset) {
    redirect('assets.php');
}

// Handle AJAX form submissions
if ($_POST && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['add_credential'])) {
        try {
            $password_encrypted = !empty($_POST['password']) ? encryptPassword($_POST['password']) : null;
            $pin_encrypted = !empty($_POST['pin']) ? encryptPassword($_POST['pin']) : null;
            
            $stmt = $pdo->prepare("INSERT INTO asset_credentials (asset_id, category_id, title, username, password_encrypted, pin_encrypted, url, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $asset_id,
                empty($_POST['category_id']) ? null : $_POST['category_id'],
                $_POST['title'],
                $_POST['username'],
                $password_encrypted,
                $pin_encrypted,
                $_POST['url'],
                $_POST['notes']
            ]);
            echo json_encode(['success' => true, 'message' => 'Credential added successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['update_credential'])) {
        try {
            // Get existing credential for password handling
            $stmt = $pdo->prepare("SELECT * FROM asset_credentials WHERE id = ? AND asset_id = ?");
            $stmt->execute([$_POST['credential_id'], $asset_id]);
            $existing = $stmt->fetch();
            
            $password_encrypted = !empty($_POST['password']) ? encryptPassword($_POST['password']) : $existing['password_encrypted'];
            $pin_encrypted = !empty($_POST['pin']) ? encryptPassword($_POST['pin']) : $existing['pin_encrypted'];
            
            $stmt = $pdo->prepare("UPDATE asset_credentials SET category_id=?, title=?, username=?, password_encrypted=?, pin_encrypted=?, url=?, notes=? WHERE id=? AND asset_id=?");
            $stmt->execute([
                empty($_POST['category_id']) ? null : $_POST['category_id'],
                $_POST['title'],
                $_POST['username'],
                $password_encrypted,
                $pin_encrypted,
                $_POST['url'],
                $_POST['notes'],
                $_POST['credential_id'],
                $asset_id
            ]);
            echo json_encode(['success' => true, 'message' => 'Credential updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['add_category'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO credential_categories (name, description) VALUES (?, ?)");
            $stmt->execute([$_POST['category_name'], $_POST['category_description']]);
            echo json_encode(['success' => true, 'message' => 'Category added successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    exit;
}

// Get credential data for editing (AJAX)
if (isset($_GET['get_credential']) && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM asset_credentials WHERE id = ? AND asset_id = ?");
    $stmt->execute([$_GET['id'], $asset_id]);
    $credential = $stmt->fetch();
    echo json_encode($credential);
    exit;
}

// Get credential categories
$stmt = $pdo->query("SELECT * FROM credential_categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get credentials for this asset
$stmt = $pdo->prepare("
    SELECT ac.*, cc.name as category_name 
    FROM asset_credentials ac 
    LEFT JOIN credential_categories cc ON ac.category_id = cc.id 
    WHERE ac.asset_id = ? 
    ORDER BY cc.name, ac.title
");
$stmt->execute([$asset_id]);
$credentials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Asset Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .content { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
        .asset-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
        .btn-small { padding: 5px 10px; font-size: 12px; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 3% auto; padding: 0; border-radius: 5px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; }
        .modal-body { padding: 20px; }
        .close { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        
        /* Form Styles */
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        .form-group textarea { height: 60px; }
        
        /* Credential Display */
        .credential-item { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #007cba; }
        .password-field { font-family: monospace; background: white; padding: 5px; border-radius: 3px; border: 1px solid #ddd; display: inline-block; }
        
        /* Alert Styles */
        .alert { padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php $page_title = 'Asset Credentials'; include 'includes/navigation.php'; ?>

    <div class="breadcrumb">
        <a href="assets.php">Assets</a> > 
        <a href="assets.php?company_id=<?php echo $asset['company_id']; ?>"><?php echo htmlspecialchars($asset['company_name']); ?></a> > 
        <?php echo htmlspecialchars($asset['name']); ?> > Credentials
    </div>

    <div class="content">
        <div class="asset-info">
            <h2>🔐 <?php echo htmlspecialchars($asset['name']); ?> - Credentials</h2>
            <p>
                <strong>Company:</strong> <?php echo htmlspecialchars($asset['company_name']); ?> | 
                <strong>Asset Tag:</strong> <?php echo htmlspecialchars($asset['asset_tag']); ?>
                <?php if ($asset['employee_name']): ?>
                    | <strong>Assigned to:</strong> <?php echo htmlspecialchars($asset['employee_name']); ?>
                <?php endif; ?>
            </p>
        </div>

        <div class="page-header">
            <h3>Stored Credentials</h3>
            <div>
                <button onclick="openAddModal()" class="btn btn-success">+ Add Credential</button>
                <button onclick="openCategoryModal()" class="btn btn-secondary">+ Add Category</button>
            </div>
        </div>

        <div id="alert-container"></div>

        <?php if (empty($credentials)): ?>
            <p>No credentials stored for this asset yet.</p>
        <?php else: ?>
            <?php foreach ($credentials as $cred): ?>
            <div class="credential-item">
                <h4><?php echo htmlspecialchars($cred['title']); ?> 
                    <?php if ($cred['category_name']): ?>
                        <small>(<?php echo htmlspecialchars($cred['category_name']); ?>)</small>
                    <?php endif; ?>
                </h4>
                
                <?php if ($cred['username']): ?>
                    <p><strong>Username:</strong> <span class="password-field"><?php echo htmlspecialchars($cred['username']); ?></span></p>
                <?php endif; ?>
                
                <?php if ($cred['password_encrypted']): ?>
                    <p><strong>Password:</strong> <span class="password-field"><?php echo htmlspecialchars(decryptPassword($cred['password_encrypted'])); ?></span></p>
                <?php endif; ?>
                
                <?php if ($cred['pin_encrypted']): ?>
                    <p><strong>PIN/Code:</strong> <span class="password-field"><?php echo htmlspecialchars(decryptPassword($cred['pin_encrypted'])); ?></span></p>
                <?php endif; ?>
                
                <?php if ($cred['url']): ?>
                    <p><strong>URL:</strong> <a href="<?php echo htmlspecialchars($cred['url']); ?>" target="_blank"><?php echo htmlspecialchars($cred['url']); ?></a></p>
                <?php endif; ?>
                
                <?php if ($cred['notes']): ?>
                    <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($cred['notes'])); ?></p>
                <?php endif; ?>
                
                <p style="margin-top: 10px;">
                    <button onclick="openEditModal(<?php echo $cred['id']; ?>)" class="btn btn-small">Edit</button>
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p style="margin-top: 30px;">
            <a href="assets.php?edit=<?php echo $asset['id']; ?>" class="btn">Edit Asset</a>
            <a href="assets.php?company_id=<?php echo $asset['company_id']; ?>" class="btn">Back to Assets</a>
        </p>
    </div>

    <!-- Credential Modal -->
    <div id="credentialModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Credential</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="credentialForm">
                    <input type="hidden" id="credential_id" name="credential_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category</label>
                            <select id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Title/Description *</label>
                            <input type="text" id="title" name="title" required placeholder="e.g., Local Admin, WiFi Password">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="username" name="username" placeholder="e.g., administrator, admin">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>PIN/Code</label>
                            <input type="password" id="pin" name="pin" placeholder="Enter PIN or code">
                        </div>
                        <div class="form-group">
                            <label>URL/Website</label>
                            <input type="url" id="url" name="url" placeholder="https://example.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="notes" name="notes" placeholder="Additional information, license keys, etc."></textarea>
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" id="submitBtn" class="btn btn-success">Add Credential</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Credential Category</h3>
                <span class="close" onclick="closeCategoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="form-group">
                        <label>Category Name *</label>
                        <input type="text" name="category_name" required placeholder="e.g., Database Access">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="category_description" placeholder="Brief description of this category">
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeCategoryModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openAddModal() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Add Credential';
        document.getElementById('submitBtn').textContent = 'Add Credential';
        document.getElementById('credentialForm').reset();
        document.getElementById('credential_id').value = '';
        document.getElementById('credentialModal').style.display = 'block';
    }

    function openEditModal(credentialId) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Credential';
        document.getElementById('submitBtn').textContent = 'Update Credential';
        
        fetch(`?get_credential=1&id=${credentialId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('credential_id').value = data.id;
                document.getElementById('category_id').value = data.category_id || '';
                document.getElementById('title').value = data.title || '';
                document.getElementById('username').value = data.username || '';
                document.getElementById('url').value = data.url || '';
                document.getElementById('notes').value = data.notes || '';
                // Don't populate password fields for security
                document.getElementById('password').placeholder = 'Leave blank to keep current';
                document.getElementById('pin').placeholder = 'Leave blank to keep current';
                document.getElementById('credentialModal').style.display = 'block';
            });
    }

    function openCategoryModal() {
        document.getElementById('categoryModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('credentialModal').style.display = 'none';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
        document.getElementById('categoryForm').reset();
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 3000);
    }

    // Handle credential form submission
    document.getElementById('credentialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        if (isEditMode) {
            formData.append('update_credential', '1');
        } else {
            formData.append('add_credential', '1');
        }
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                closeModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'error');
        });
    });

    // Handle category form submission
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('add_category', '1');
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                closeCategoryModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'error');
        });
    });

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html>