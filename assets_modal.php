<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Get company filter
$company_filter = isset($_GET['company_id']) ? $_GET['company_id'] : '';

// Handle AJAX form submissions
if ($_POST && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['add_asset'])) {
        try {
            // Generate auto-incrementing asset tag if not provided
            $asset_tag = $_POST['asset_tag'];
            if (empty($asset_tag)) {
                $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(asset_tag, 4) AS UNSIGNED)) as max_num FROM assets WHERE asset_tag LIKE 'AST-%'");
                $result = $stmt->fetch();
                $next_num = ($result['max_num'] ?? 0) + 1;
                $asset_tag = 'AST-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
            }
            
            $stmt = $pdo->prepare("INSERT INTO assets (company_id, category_id, name, make, model, serial_number, asset_tag, location, employee_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['company_id'],
                empty($_POST['category_id']) ? null : $_POST['category_id'],
                $_POST['name'],
                $_POST['make'],
                $_POST['model'],
                $_POST['serial_number'],
                $asset_tag,
                $_POST['location'],
                empty($_POST['employee_id']) ? null : $_POST['employee_id'],
                $_POST['notes']
            ]);
            echo json_encode(['success' => true, 'message' => "Asset added successfully! Asset Tag: $asset_tag"]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['update_asset'])) {
        try {
            $stmt = $pdo->prepare("UPDATE assets SET company_id=?, category_id=?, name=?, make=?, model=?, serial_number=?, asset_tag=?, location=?, employee_id=?, notes=? WHERE id=?");
            $stmt->execute([
                $_POST['company_id'],
                empty($_POST['category_id']) ? null : $_POST['category_id'],
                $_POST['name'],
                $_POST['make'],
                $_POST['model'],
                $_POST['serial_number'],
                $_POST['asset_tag'],
                $_POST['location'],
                empty($_POST['employee_id']) ? null : $_POST['employee_id'],
                $_POST['notes'],
                $_POST['asset_id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Asset updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['add_new_employee']) && !empty($_POST['new_employee_name']) && !empty($_POST['company_id'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees (company_id, name) VALUES (?, ?)");
            $stmt->execute([$_POST['company_id'], $_POST['new_employee_name']]);
            $employee_id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Employee added!', 'employee_id' => $employee_id, 'employee_name' => $_POST['new_employee_name']]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    exit;
}

// Get asset data for editing (AJAX)
if (isset($_GET['get_asset']) && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $asset = $stmt->fetch();
    echo json_encode($asset);
    exit;
}

// Get employees for company (AJAX)
if (isset($_GET['get_employees']) && isset($_GET['company_id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT id, name FROM employees WHERE company_id = ? ORDER BY name");
    $stmt->execute([$_GET['company_id']]);
    $employees = $stmt->fetchAll();
    echo json_encode($employees);
    exit;
}

// Get companies for dropdown
$stmt = $pdo->query("SELECT id, name FROM companies WHERE status = 'active' ORDER BY name");
$companies = $stmt->fetchAll();

// Get asset categories
$stmt = $pdo->query("SELECT id, name FROM asset_categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get assets with company, employee info, and credential count
$where_clause = $company_filter ? "WHERE a.company_id = " . intval($company_filter) : "";
$stmt = $pdo->query("
    SELECT a.*, c.name as company_name, cat.name as category_name, e.name as employee_name, 
           COUNT(ac.id) as credential_count
    FROM assets a 
    JOIN companies c ON a.company_id = c.id 
    LEFT JOIN asset_categories cat ON a.category_id = cat.id 
    LEFT JOIN employees e ON a.employee_id = e.id
    LEFT JOIN asset_credentials ac ON a.id = ac.asset_id
    $where_clause
    GROUP BY a.id
    ORDER BY c.name, a.name
");
$assets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Assets</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .content { background: white; padding: 20px; border-radius: 5px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .filter { margin-bottom: 20px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 2% auto; padding: 0; border-radius: 5px; width: 90%; max-width: 800px; max-height: 95vh; overflow-y: auto; }
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
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-active { color: #28a745; }
        .status-repair { color: #ffc107; }
        .status-retired { color: #dc3545; }
        .actions a { margin-right: 10px; color: #007cba; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
        
        /* Alert Styles */
        .alert { padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        #new_employee_form { display: none; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 3px; }
    </style>
</head>
<body>
    <?php $page_title = 'Assets'; include 'includes/navigation.php'; ?>

    <div class="content">
        <div class="page-header">
            <h2>Assets</h2>
            <button onclick="openAddModal()" class="btn btn-success">+ Add Asset</button>
        </div>

        <div class="filter">
            <label>Filter by Company:</label>
            <select onchange="window.location.href='assets.php?company_id=' + this.value">
                <option value="">All Companies</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo $company['id']; ?>" <?php echo $company_filter == $company['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($company['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="alert-container"></div>

        <table>
            <tr>
                <th>Asset</th>
                <th>Company</th>
                <th>Category</th>
                <th>Make/Model</th>
                <th>Location/Assigned To</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($assets as $asset): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($asset['name']); ?></strong>
                    <?php if ($asset['asset_tag']): ?>
                        <br><small>Tag: <?php echo htmlspecialchars($asset['asset_tag']); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($asset['company_name']); ?></td>
                <td><?php echo htmlspecialchars($asset['category_name'] ?: 'Uncategorized'); ?></td>
                <td>
                    <?php if ($asset['make'] || $asset['model']): ?>
                        <?php echo htmlspecialchars($asset['make'] . ' ' . $asset['model']); ?>
                    <?php endif; ?>
                    <?php if ($asset['serial_number']): ?>
                        <br><small>S/N: <?php echo htmlspecialchars($asset['serial_number']); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($asset['location']): ?>
                        <strong>Location:</strong> <?php echo htmlspecialchars($asset['location']); ?><br>
                    <?php endif; ?>
                    <?php if ($asset['employee_name']): ?>
                        <strong>Assigned to:</strong> <?php echo htmlspecialchars($asset['employee_name']); ?>
                    <?php endif; ?>
                </td>
                <td class="status-<?php echo $asset['status']; ?>">
                    <?php echo ucfirst($asset['status']); ?>
                </td>
                <td class="actions">
                    <a href="#" onclick="openEditModal(<?php echo $asset['id']; ?>)">Edit</a>
                    <a href="credentials.php?asset_id=<?php echo $asset['id']; ?>" style="color: #dc3545;">🔒 Credentials</a>
                    <?php if ($asset['credential_count'] > 0): ?>
                        <small>(<?php echo $asset['credential_count']; ?>)</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Asset Modal -->
    <div id="assetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Asset</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="assetForm">
                    <input type="hidden" id="asset_id" name="asset_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company *</label>
                            <select id="company_id" name="company_id" required onchange="loadEmployees()">
                                <option value="">Select Company</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>">
                                        <?php echo htmlspecialchars($company['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Asset Name *</label>
                            <input type="text" id="name" name="name" required placeholder="e.g., Reception Desk Computer">
                        </div>
                        <div class="form-group">
                            <label>Asset Tag</label>
                            <input type="text" id="asset_tag" name="asset_tag" placeholder="Leave blank for auto: AST-0001">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Make</label>
                            <input type="text" id="make" name="make" placeholder="e.g., Dell, HP, Cisco">
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input type="text" id="model" name="model" placeholder="e.g., OptiPlex 7090">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number">
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" id="location" name="location" placeholder="e.g., Front Desk, Server Room">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Assigned To (Employee)</label>
                        <select id="employee_id" name="employee_id">
                            <option value="">Unassigned</option>
                            <option value="new">+ Add New Employee</option>
                        </select>
                        
                        <div id="new_employee_form">
                            <label>New Employee Name:</label>
                            <input type="text" id="new_employee_name" name="new_employee_name" placeholder="Enter employee name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="notes" name="notes" placeholder="Configuration details, warranty info, etc."></textarea>
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" id="submitBtn" class="btn btn-success">Add Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openAddModal() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Add Asset';
        document.getElementById('submitBtn').textContent = 'Add Asset';
        document.getElementById('assetForm').reset();
        document.getElementById('asset_id').value = '';
        
        // Pre-select company if filtered
        <?php if ($company_filter): ?>
        document.getElementById('company_id').value = '<?php echo $company_filter; ?>';
        loadEmployees();
        <?php endif; ?>
        
        document.getElementById('assetModal').style.display = 'block';
    }

    function openEditModal(assetId) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Asset';
        document.getElementById('submitBtn').textContent = 'Update Asset';
        
        fetch(`?get_asset=1&id=${assetId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('asset_id').value = data.id;
                document.getElementById('company_id').value = data.company_id || '';
                document.getElementById('category_id').value = data.category_id || '';
                document.getElementById('name').value = data.name || '';
                document.getElementById('asset_tag').value = data.asset_tag || '';
                document.getElementById('make').value = data.make || '';
                document.getElementById('model').value = data.model || '';
                document.getElementById('serial_number').value = data.serial_number || '';
                document.getElementById('location').value = data.location || '';
                document.getElementById('notes').value = data.notes || '';
                
                loadEmployees().then(() => {
                    document.getElementById('employee_id').value = data.employee_id || '';
                });
                
                document.getElementById('assetModal').style.display = 'block';
            });
    }

    function loadEmployees() {
        const companyId = document.getElementById('company_id').value;
        const employeeSelect = document.getElementById('employee_id');
        
        // Clear existing options except default ones
        employeeSelect.innerHTML = '<option value="">Unassigned</option><option value="new">+ Add New Employee</option>';
        
        if (companyId) {
            return fetch(`?get_employees=1&company_id=${companyId}`)
                .then(response => response.json())
                .then(employees => {
                    employees.forEach(employee => {
                        const option = document.createElement('option');
                        option.value = employee.id;
                        option.textContent = employee.name;
                        employeeSelect.insertBefore(option, employeeSelect.lastElementChild);
                    });
                });
        }
        return Promise.resolve();
    }

    function closeModal() {
        document.getElementById('assetModal').style.display = 'none';
        document.getElementById('new_employee_form').style.display = 'none';
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 3000);
    }

    // Handle employee dropdown change
    document.getElementById('employee_id').addEventListener('change', function() {
        const newForm = document.getElementById('new_employee_form');
        if (this.value === 'new') {
            newForm.style.display = 'block';
        } else {
            newForm.style.display = 'none';
        }
    });

    // Handle form submission
    document.getElementById('assetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Handle new employee creation
        if (document.getElementById('employee_id').value === 'new' && document.getElementById('new_employee_name').value) {
            formData.append('add_new_employee', '1');
        }
        
        if (isEditMode) {
            formData.append('update_asset', '1');
        } else {
            formData.append('add_asset', '1');
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

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('assetModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html>