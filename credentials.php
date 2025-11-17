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

// Get credential for editing
$edit_credential = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM asset_credentials WHERE id = ? AND asset_id = ?");
    $stmt->execute([$_GET['edit'], $asset_id]);
    $edit_credential = $stmt->fetch();
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_category'])) {
        $stmt = $pdo->prepare("INSERT INTO credential_categories (name, description) VALUES (?, ?)");
        $stmt->execute([$_POST['category_name'], $_POST['category_description']]);
        $success = "Category added successfully!";
    }
    
    if (isset($_POST['add_credential'])) {
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
        $success = "Credential added successfully!";
    }
    
    if (isset($_POST['update_credential'])) {
        $password_encrypted = !empty($_POST['password']) ? encryptPassword($_POST['password']) : $edit_credential['password_encrypted'];
        $pin_encrypted = !empty($_POST['pin']) ? encryptPassword($_POST['pin']) : $edit_credential['pin_encrypted'];
        
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
        $success = "Credential updated successfully!";
        $edit_credential = null;
    }
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
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .content { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .form-group textarea { height: 60px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-small { padding: 5px 10px; font-size: 12px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
        .asset-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .credential-item { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #007cba; }
        .password-field { font-family: monospace; background: white; padding: 5px; border-radius: 3px; border: 1px solid #ddd; display: inline-block; }
        .toggle-section { margin-top: 20px; }
        .toggle-btn { background: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?> - Asset Credentials</h1>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="companies.php">Companies</a>
            <a href="assets.php">Assets</a>
            <a href="work_orders.php">Work Orders</a>
            <a href="invoices.php">Invoices</a>
            <a href="payments.php">Payments</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

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

        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <h3><?php echo $edit_credential ? 'Edit Credential' : 'Add New Credential'; ?></h3>
        <form method="POST">
            <?php if ($edit_credential): ?>
                <input type="hidden" name="credential_id" value="<?php echo $edit_credential['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo $edit_credential && $edit_credential['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Title/Description *</label>
                    <input type="text" name="title" value="<?php echo $edit_credential ? htmlspecialchars($edit_credential['title']) : ''; ?>" required placeholder="e.g., Local Admin, WiFi Password">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $edit_credential ? htmlspecialchars($edit_credential['username']) : ''; ?>" placeholder="e.g., administrator, admin">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="<?php echo $edit_credential && $edit_credential['password_encrypted'] ? 'Leave blank to keep current' : 'Enter password'; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>PIN/Code</label>
                    <input type="password" name="pin" placeholder="<?php echo $edit_credential && $edit_credential['pin_encrypted'] ? 'Leave blank to keep current' : 'Enter PIN or code'; ?>">
                </div>
                <div class="form-group">
                    <label>URL/Website</label>
                    <input type="url" name="url" value="<?php echo $edit_credential ? htmlspecialchars($edit_credential['url']) : ''; ?>" placeholder="https://example.com">
                </div>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" placeholder="Additional information, license keys, etc."><?php echo $edit_credential ? htmlspecialchars($edit_credential['notes']) : ''; ?></textarea>
            </div>
            
            <?php if ($edit_credential): ?>
                <button type="submit" name="update_credential" class="btn">Update Credential</button>
                <a href="credentials.php?asset_id=<?php echo $asset_id; ?>" class="btn" style="background: #6c757d;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_credential" class="btn">Add Credential</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="content">
        <h3>Stored Credentials</h3>
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
                    <a href="credentials.php?asset_id=<?php echo $asset_id; ?>&edit=<?php echo $cred['id']; ?>" class="btn btn-small">Edit</a>
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="toggle-section">
        <button onclick="toggleCategoryForm()" class="btn toggle-btn">+ Add New Category</button>
        <div id="category-form" style="display: none; margin-top: 15px; background: white; padding: 20px; border-radius: 5px;">
            <h4>Add Credential Category</h4>
            <form method="POST">
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="category_name" required placeholder="e.g., Database Access">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="category_description" placeholder="Brief description of this category">
                </div>
                <button type="submit" name="add_category" class="btn">Add Category</button>
            </form>
        </div>
    </div>

    <script>
    function toggleCategoryForm() {
        var form = document.getElementById('category-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    </script>

    <p style="margin-top: 30px;">
        <a href="assets.php?edit=<?php echo $asset['id']; ?>" class="btn">Edit Asset</a>
        <a href="assets.php?company_id=<?php echo $asset['company_id']; ?>" class="btn">Back to Assets</a>
    </p>
</body>
</html>