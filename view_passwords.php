<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

$asset_id = $_GET['asset_id'] ?? null;
if (!$asset_id) {
    redirect('assets.php');
}

// Get asset with passwords
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
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Asset Passwords</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .content { background: white; padding: 20px; border-radius: 5px; }
        .password-field { background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 5px; border-left: 4px solid #dc3545; }
        .password-field label { font-weight: bold; color: #dc3545; display: block; margin-bottom: 5px; }
        .password-value { font-family: monospace; background: white; padding: 8px; border-radius: 3px; border: 1px solid #ddd; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
        .asset-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?> - Asset Passwords</h1>
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
        <?php echo htmlspecialchars($asset['name']); ?> > Passwords
    </div>

    <div class="content">
        <div class="asset-info">
            <h2><?php echo htmlspecialchars($asset['name']); ?> - Passwords & Credentials</h2>
            <p>
                <strong>Company:</strong> <?php echo htmlspecialchars($asset['company_name']); ?><br>
                <strong>Asset Tag:</strong> <?php echo htmlspecialchars($asset['asset_tag']); ?><br>
                <?php if ($asset['employee_name']): ?>
                    <strong>Assigned to:</strong> <?php echo htmlspecialchars($asset['employee_name']); ?><br>
                <?php endif; ?>
                <strong>Make/Model:</strong> <?php echo htmlspecialchars($asset['make'] . ' ' . $asset['model']); ?>
            </p>
        </div>

        <?php if ($asset['admin_username'] || $asset['admin_password_encrypted']): ?>
        <div class="password-field">
            <label>🔐 Administrator Account</label>
            <?php if ($asset['admin_username']): ?>
                <div><strong>Username:</strong> <span class="password-value"><?php echo htmlspecialchars($asset['admin_username']); ?></span></div>
            <?php endif; ?>
            <?php if ($asset['admin_password_encrypted']): ?>
                <div><strong>Password:</strong> <span class="password-value"><?php echo htmlspecialchars(decryptPassword($asset['admin_password_encrypted'])); ?></span></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($asset['wifi_password_encrypted']): ?>
        <div class="password-field">
            <label>📶 WiFi Password</label>
            <div class="password-value"><?php echo htmlspecialchars(decryptPassword($asset['wifi_password_encrypted'])); ?></div>
        </div>
        <?php endif; ?>

        <?php if ($asset['other_passwords']): ?>
        <div class="password-field">
            <label>📝 Other Passwords & Notes</label>
            <div class="password-value" style="white-space: pre-wrap;"><?php echo htmlspecialchars($asset['other_passwords']); ?></div>
        </div>
        <?php endif; ?>

        <?php if (!$asset['admin_password_encrypted'] && !$asset['wifi_password_encrypted'] && !$asset['other_passwords']): ?>
        <p>No passwords stored for this asset. <a href="assets.php?edit=<?php echo $asset['id']; ?>">Add passwords</a></p>
        <?php endif; ?>

        <p style="margin-top: 30px;">
            <a href="assets.php?edit=<?php echo $asset['id']; ?>" class="nav a">Edit Asset & Passwords</a>
            <a href="assets.php?company_id=<?php echo $asset['company_id']; ?>" class="nav a">Back to Assets</a>
        </p>
    </div>
</body>
</html>