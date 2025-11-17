<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

$company_id = $_GET['company_id'] ?? null;
if (!$company_id) {
    redirect('companies.php');
}

// Get company info
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch();

if (!$company) {
    redirect('companies.php');
}

// Get employee for editing
$edit_employee = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ? AND company_id = ?");
    $stmt->execute([$_GET['edit'], $company_id]);
    $edit_employee = $stmt->fetch();
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_employee'])) {
        $stmt = $pdo->prepare("INSERT INTO employees (company_id, name, email, phone, department, title) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $company_id,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['department'],
            $_POST['title']
        ]);
        $success = "Employee added successfully!";
    }
    
    if (isset($_POST['update_employee'])) {
        $stmt = $pdo->prepare("UPDATE employees SET name=?, email=?, phone=?, department=?, title=? WHERE id=? AND company_id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['department'],
            $_POST['title'],
            $_POST['employee_id'],
            $company_id
        ]);
        $success = "Employee updated successfully!";
        $edit_employee = null;
    }
}

// Get employees for this company with asset counts
$stmt = $pdo->prepare("
    SELECT e.*, COUNT(a.id) as asset_count 
    FROM employees e 
    LEFT JOIN assets a ON e.id = a.employee_id 
    WHERE e.company_id = ? 
    GROUP BY e.id 
    ORDER BY e.name
");
$stmt->execute([$company_id]);
$employees = $stmt->fetchAll();

// Get assets assigned to employees
$stmt = $pdo->prepare("
    SELECT a.*, e.name as employee_name 
    FROM assets a 
    JOIN employees e ON a.employee_id = e.id 
    WHERE e.company_id = ? 
    ORDER BY e.name, a.name
");
$stmt->execute([$company_id]);
$assigned_assets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Employees</title>
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
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
    </style>
</head>
<body>
    <?php $page_title = 'Employees'; include 'includes/navigation.php'; ?>

    <div class="breadcrumb">
        <a href="companies.php">Companies</a> > 
        <a href="companies.php?edit=<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></a> > 
        Employees
    </div>

    <div class="content">
        <h2><?php echo $edit_employee ? 'Edit Employee' : 'Add Employee'; ?> - <?php echo htmlspecialchars($company['name']); ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php if ($edit_employee): ?>
                <input type="hidden" name="employee_id" value="<?php echo $edit_employee['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Employee Name *</label>
                    <input type="text" name="name" value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['phone']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['department']) : ''; ?>" placeholder="e.g., Accounting, Sales">
                </div>
            </div>
            
            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['title']) : ''; ?>" placeholder="e.g., Manager, Receptionist">
            </div>
            
            <?php if ($edit_employee): ?>
                <button type="submit" name="update_employee" class="btn">Update Employee</button>
                <a href="employees.php?company_id=<?php echo $company_id; ?>" class="btn" style="background: #6c757d; text-decoration: none;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_employee" class="btn">Add Employee</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="content">
        <h2>Employees</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Department/Title</th>
                <th>Contact</th>
                <th>Assets</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td><strong><a href="employee_detail.php?id=<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></a></strong></td>
                <td>
                    <?php if ($employee['department']): ?>
                        <?php echo htmlspecialchars($employee['department']); ?>
                    <?php endif; ?>
                    <?php if ($employee['title']): ?>
                        <br><small><?php echo htmlspecialchars($employee['title']); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($employee['email']): ?>
                        <?php echo htmlspecialchars($employee['email']); ?><br>
                    <?php endif; ?>
                    <?php if ($employee['phone']): ?>
                        <?php echo htmlspecialchars($employee['phone']); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $employee['asset_count']; ?> assets</td>
                <td>
                    <a href="employees.php?company_id=<?php echo $company_id; ?>&edit=<?php echo $employee['id']; ?>">Edit</a> |
                    <a href="assets.php?company_id=<?php echo $company_id; ?>&employee_id=<?php echo $employee['id']; ?>">View Assets</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php if (!empty($assigned_assets)): ?>
    <div class="content">
        <h2>Asset Assignments</h2>
        <table>
            <tr>
                <th>Employee</th>
                <th>Asset</th>
                <th>Asset Tag</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($assigned_assets as $asset): ?>
            <tr>
                <td><?php echo htmlspecialchars($asset['employee_name']); ?></td>
                <td><?php echo htmlspecialchars($asset['name']); ?></td>
                <td><?php echo htmlspecialchars($asset['asset_tag']); ?></td>
                <td><?php echo htmlspecialchars($asset['category_name'] ?? 'Uncategorized'); ?></td>
                <td>
                    <a href="assets.php?edit=<?php echo $asset['id']; ?>">Edit Asset</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
</body>
</html>