<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Get company for editing
$edit_company = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_company = $stmt->fetch();
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_company'])) {
        $stmt = $pdo->prepare("INSERT INTO companies (name, email, phone, address, monthly_rate, billing_contact, technical_contact, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            empty($_POST['monthly_rate']) ? 0 : $_POST['monthly_rate'],
            $_POST['billing_contact'],
            $_POST['technical_contact'],
            $_POST['notes']
        ]);
        $success = "Company added successfully!";
    }
    
    if (isset($_POST['update_company'])) {
        $stmt = $pdo->prepare("UPDATE companies SET name=?, email=?, phone=?, address=?, monthly_rate=?, billing_contact=?, technical_contact=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            empty($_POST['monthly_rate']) ? 0 : $_POST['monthly_rate'],
            $_POST['billing_contact'],
            $_POST['technical_contact'],
            $_POST['notes'],
            $_POST['company_id']
        ]);
        $success = "Company updated successfully!";
        $edit_company = null;
    }
}

// Get all companies
$stmt = $pdo->query("SELECT * FROM companies ORDER BY name");
$companies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Companies</title>
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
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .form-group textarea { height: 60px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; }
        .currency { text-align: right; }
    </style>
</head>
<body>
    <?php $page_title = 'Companies'; include 'includes/navigation.php'; ?>

    <div class="content">
        <h2><?php echo $edit_company ? 'Edit Company' : 'Add New Company'; ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php if ($edit_company): ?>
                <input type="hidden" name="company_id" value="<?php echo $edit_company['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" name="name" value="<?php echo $edit_company ? htmlspecialchars($edit_company['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Monthly Rate</label>
                    <input type="number" name="monthly_rate" step="0.01" value="<?php echo $edit_company ? $edit_company['monthly_rate'] : ''; ?>" placeholder="125.00">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $edit_company ? htmlspecialchars($edit_company['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo $edit_company ? htmlspecialchars($edit_company['phone']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address"><?php echo $edit_company ? htmlspecialchars($edit_company['address']) : ''; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Billing Contact</label>
                    <input type="text" name="billing_contact" value="<?php echo $edit_company ? htmlspecialchars($edit_company['billing_contact']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Technical Contact</label>
                    <input type="text" name="technical_contact" value="<?php echo $edit_company ? htmlspecialchars($edit_company['technical_contact']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" placeholder="Contract details, special pricing notes, etc."><?php echo $edit_company ? htmlspecialchars($edit_company['notes']) : ''; ?></textarea>
            </div>
            
            <?php if ($edit_company): ?>
                <button type="submit" name="update_company" class="btn">Update Company</button>
                <a href="companies.php" class="btn" style="background: #6c757d; text-decoration: none;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_company" class="btn">Add Company</button>
            <?php endif; ?>
        </form>

        <h2>Existing Companies</h2>
        <table>
            <tr>
                <th>Company Name</th>
                <th>Monthly Rate</th>
                <th>Contact Info</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($companies as $company): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($company['name']); ?></strong>
                    <?php if ($company['notes']): ?>
                        <br><small><?php echo htmlspecialchars(substr($company['notes'], 0, 50)); ?>...</small>
                    <?php endif; ?>
                </td>
                <td class="currency"><?php echo formatCurrency($company['monthly_rate']); ?></td>
                <td>
                    <?php if ($company['email']): ?>
                        <?php echo htmlspecialchars($company['email']); ?><br>
                    <?php endif; ?>
                    <?php if ($company['phone']): ?>
                        <?php echo htmlspecialchars($company['phone']); ?>
                    <?php endif; ?>
                </td>
                <td class="status-<?php echo $company['status']; ?>">
                    <?php echo ucfirst($company['status']); ?>
                </td>
                <td><?php echo formatDate($company['created_at']); ?></td>
                <td>
                    <a href="companies.php?edit=<?php echo $company['id']; ?>">Edit</a> |
                    <a href="employees.php?company_id=<?php echo $company['id']; ?>">Employees</a> |
                    <a href="assets.php?company_id=<?php echo $company['id']; ?>">Assets</a> |
                    <a href="work_orders.php?company_id=<?php echo $company['id']; ?>">Orders</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>