<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Get company filter
$company_filter = isset($_GET['company_id']) ? $_GET['company_id'] : '';

// Get asset for editing
$edit_asset = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_asset = $stmt->fetch();
}

// Handle form submissions
if ($_POST) {
    // Handle adding new employee from asset form
    if (isset($_POST['add_new_employee']) && !empty($_POST['new_employee_name']) && !empty($_POST['company_id'])) {
        $stmt = $pdo->prepare("INSERT INTO employees (company_id, name) VALUES (?, ?)");
        $stmt->execute([$_POST['company_id'], $_POST['new_employee_name']]);
        $_POST['employee_id'] = $pdo->lastInsertId();
        $success = "Employee added and assigned to asset!";
    }
    
    if (isset($_POST['add_asset'])) {
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
        $success = "Asset added successfully! Asset Tag: $asset_tag";
    }
    
    if (isset($_POST['update_asset'])) {
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
        $success = "Asset updated successfully!";
        $edit_asset = null;
    }
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

// Get employees for selected company
$employees = [];
if ($company_filter) {
    $stmt = $pdo->prepare("SELECT id, name FROM employees WHERE company_id = ? ORDER BY name");
    $stmt->execute([$company_filter]);
    $employees = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Assets</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .nav a.active { background: #005a87; }
        .content { background: white; padding: 20px; border-radius: 5px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .form-group textarea { height: 60px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-active { color: #28a745; }
        .status-repair { color: #ffc107; }
        .status-retired { color: #dc3545; }
        .filter { margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php $page_title = 'Assets'; include 'includes/navigation.php'; ?>

    <div class="content">
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

        <h2><?php echo $edit_asset ? 'Edit Asset' : 'Add New Asset'; ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php if ($edit_asset): ?>
                <input type="hidden" name="asset_id" value="<?php echo $edit_asset['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Company *</label>
                    <select name="company_id" required>
                        <option value="">Select Company</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo $company['id']; ?>" 
                                <?php echo ($edit_asset && $edit_asset['company_id'] == $company['id']) || $company_filter == $company['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo $edit_asset && $edit_asset['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Asset Name *</label>
                    <input type="text" name="name" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['name']) : ''; ?>" required placeholder="e.g., Reception Desk Computer">
                </div>
                <div class="form-group">
                    <label>Asset Tag</label>
                    <input type="text" name="asset_tag" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['asset_tag']) : ''; ?>" placeholder="Leave blank for auto: AST-0001">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Make</label>
                    <input type="text" name="make" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['make']) : ''; ?>" placeholder="e.g., Dell, HP, Cisco">
                </div>
                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="model" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['model']) : ''; ?>" placeholder="e.g., OptiPlex 7090">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['serial_number']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo $edit_asset ? htmlspecialchars($edit_asset['location']) : ''; ?>" placeholder="e.g., Front Desk, Server Room">
                </div>
            </div>
            
            <div class="form-group">
                <label>Assigned To (Employee)</label>
                <select name="employee_id" id="employee_select">
                    <option value="">Unassigned</option>
                    <?php if ($company_filter): ?>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo $employee['id']; ?>" 
                                <?php echo $edit_asset && $edit_asset['employee_id'] == $employee['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($employee['name']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="new">+ Add New Employee</option>
                    <?php else: ?>
                        <option value="">Select a company first</option>
                    <?php endif; ?>
                </select>
                
                <div id="new_employee_form" style="display: none; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 3px;">
                    <label>New Employee Name:</label>
                    <input type="text" name="new_employee_name" placeholder="Enter employee name">
                    <input type="hidden" name="add_new_employee" value="1">
                </div>
                
                <?php if ($company_filter): ?>
                    <small><a href="employees.php?company_id=<?php echo $company_filter; ?>">Manage All Employees</a></small>
                <?php endif; ?>
            </div>
            
            <script>
            document.getElementById('employee_select').addEventListener('change', function() {
                var newForm = document.getElementById('new_employee_form');
                if (this.value === 'new') {
                    newForm.style.display = 'block';
                    newForm.querySelector('input[name="new_employee_name"]').required = true;
                } else {
                    newForm.style.display = 'none';
                    newForm.querySelector('input[name="new_employee_name"]').required = false;
                }
            });
            </script>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" placeholder="Configuration details, warranty info, etc."><?php echo $edit_asset ? htmlspecialchars($edit_asset['notes']) : ''; ?></textarea>
            </div>
            
            <?php if ($edit_asset): ?>
                <button type="submit" name="update_asset" class="btn">Update Asset</button>
                <a href="assets.php<?php echo $company_filter ? '?company_id=' . $company_filter : ''; ?>" class="btn" style="background: #6c757d;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_asset" class="btn">Add Asset</button>
            <?php endif; ?>
        </form>

        <h2>Assets <?php echo $company_filter ? '- ' . htmlspecialchars($companies[array_search($company_filter, array_column($companies, 'id'))]['name'] ?? '') : ''; ?></h2>
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
                    <strong><a href="asset_detail.php?id=<?php echo $asset['id']; ?>"><?php echo htmlspecialchars($asset['name']); ?></a></strong>
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
                <td>
                    <a href="assets.php?edit=<?php echo $asset['id']; ?><?php echo $company_filter ? '&company_id=' . $company_filter : ''; ?>">Edit</a> |
                    <a href="work_orders.php?asset_id=<?php echo $asset['id']; ?>">Orders</a> |
                    <a href="credentials.php?asset_id=<?php echo $asset['id']; ?>" style="color: #dc3545;">🔒 Credentials</a>
                    <?php if ($asset['credential_count'] > 0): ?>
                        <small>(<?php echo $asset['credential_count']; ?>)</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>