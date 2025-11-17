<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    // Search companies
    $stmt = $pdo->prepare("
        SELECT 'company' as type, id, name, email, phone, monthly_rate, status
        FROM companies 
        WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
        ORDER BY name
    ");
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results['companies'] = $stmt->fetchAll();

    // Search employees
    $stmt = $pdo->prepare("
        SELECT 'employee' as type, e.id, e.name, e.email, e.phone, e.department, e.title,
               c.name as company_name, c.id as company_id
        FROM employees e
        JOIN companies c ON e.company_id = c.id
        WHERE e.name LIKE ? OR e.email LIKE ? OR e.phone LIKE ? OR e.department LIKE ? OR e.title LIKE ?
        ORDER BY e.name
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results['employees'] = $stmt->fetchAll();

    // Search assets
    $stmt = $pdo->prepare("
        SELECT 'asset' as type, a.id, a.name, a.asset_tag, a.make, a.model, a.serial_number, a.location,
               c.name as company_name, c.id as company_id, e.name as employee_name
        FROM assets a
        JOIN companies c ON a.company_id = c.id
        LEFT JOIN employees e ON a.employee_id = e.id
        WHERE a.name LIKE ? OR a.asset_tag LIKE ? OR a.make LIKE ? OR a.model LIKE ? OR a.serial_number LIKE ? OR a.location LIKE ?
        ORDER BY a.name
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results['assets'] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Search Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .search-box { margin-bottom: 20px; }
        .search-box input { width: 300px; padding: 10px; font-size: 16px; border: 1px solid #ddd; border-radius: 3px; }
        .search-box button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .content { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .result-section { margin-bottom: 30px; }
        .result-section h3 { color: #007cba; border-bottom: 2px solid #007cba; padding-bottom: 5px; }
        .result-item { padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px; }
        .result-item:hover { background: #f8f9fa; }
        .result-title { font-weight: bold; font-size: 16px; }
        .result-meta { color: #666; font-size: 14px; margin-top: 5px; }
        .result-actions { margin-top: 10px; }
        .result-actions a { background: #007cba; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-right: 5px; font-size: 12px; }
        .result-actions a:hover { background: #005a87; }
        .no-results { text-align: center; color: #666; padding: 40px; }
        .search-stats { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php $page_title = 'Search'; include 'includes/navigation.php'; ?>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search companies, employees, assets..." autofocus>
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (!empty($query)): ?>
        <div class="search-stats">
            Search results for: <strong>"<?php echo htmlspecialchars($query); ?>"</strong>
        </div>

        <?php if (!empty($results['companies'])): ?>
        <div class="content">
            <div class="result-section">
                <h3>🏢 Companies (<?php echo count($results['companies']); ?>)</h3>
                <?php foreach ($results['companies'] as $company): ?>
                <div class="result-item">
                    <div class="result-title"><?php echo htmlspecialchars($company['name']); ?></div>
                    <div class="result-meta">
                        Monthly Rate: <?php echo formatCurrency($company['monthly_rate']); ?> | 
                        Status: <?php echo ucfirst($company['status']); ?>
                        <?php if ($company['email']): ?>
                            | <?php echo htmlspecialchars($company['email']); ?>
                        <?php endif; ?>
                        <?php if ($company['phone']): ?>
                            | <?php echo htmlspecialchars($company['phone']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="result-actions">
                        <a href="company_detail.php?id=<?php echo $company['id']; ?>">View Company</a>
                        <a href="companies.php" onclick="openCompanyEdit(<?php echo $company['id']; ?>); return false;">Edit</a>
                        <a href="assets.php?company_id=<?php echo $company['id']; ?>">Assets</a>
                        <a href="employees.php?company_id=<?php echo $company['id']; ?>">Employees</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($results['employees'])): ?>
        <div class="content">
            <div class="result-section">
                <h3>👤 Employees (<?php echo count($results['employees']); ?>)</h3>
                <?php foreach ($results['employees'] as $employee): ?>
                <div class="result-item">
                    <div class="result-title"><?php echo htmlspecialchars($employee['name']); ?></div>
                    <div class="result-meta">
                        Company: <?php echo htmlspecialchars($employee['company_name']); ?>
                        <?php if ($employee['department']): ?>
                            | <?php echo htmlspecialchars($employee['department']); ?>
                        <?php endif; ?>
                        <?php if ($employee['title']): ?>
                            | <?php echo htmlspecialchars($employee['title']); ?>
                        <?php endif; ?>
                        <?php if ($employee['email']): ?>
                            | <?php echo htmlspecialchars($employee['email']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="result-actions">
                        <a href="company_detail.php?id=<?php echo $employee['company_id']; ?>">View Company</a>
                        <a href="employees.php?company_id=<?php echo $employee['company_id']; ?>" onclick="openEmployeeEdit(<?php echo $employee['id']; ?>, <?php echo $employee['company_id']; ?>); return false;">Edit Employee</a>
                        <a href="assets.php?company_id=<?php echo $employee['company_id']; ?>&employee_id=<?php echo $employee['id']; ?>">Their Assets</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($results['assets'])): ?>
        <div class="content">
            <div class="result-section">
                <h3>💻 Assets (<?php echo count($results['assets']); ?>)</h3>
                <?php foreach ($results['assets'] as $asset): ?>
                <div class="result-item">
                    <div class="result-title"><?php echo htmlspecialchars($asset['name']); ?></div>
                    <div class="result-meta">
                        Company: <?php echo htmlspecialchars($asset['company_name']); ?>
                        <?php if ($asset['asset_tag']): ?>
                            | Tag: <?php echo htmlspecialchars($asset['asset_tag']); ?>
                        <?php endif; ?>
                        <?php if ($asset['make'] || $asset['model']): ?>
                            | <?php echo htmlspecialchars($asset['make'] . ' ' . $asset['model']); ?>
                        <?php endif; ?>
                        <?php if ($asset['employee_name']): ?>
                            | Assigned to: <?php echo htmlspecialchars($asset['employee_name']); ?>
                        <?php endif; ?>
                        <?php if ($asset['location']): ?>
                            | Location: <?php echo htmlspecialchars($asset['location']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="result-actions">
                        <a href="company_detail.php?id=<?php echo $asset['company_id']; ?>">View Company</a>
                        <a href="assets.php" onclick="openAssetEdit(<?php echo $asset['id']; ?>); return false;">Edit Asset</a>
                        <a href="credentials.php?asset_id=<?php echo $asset['id']; ?>">Credentials</a>
                        <a href="work_orders.php?asset_id=<?php echo $asset['id']; ?>">Work Orders</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($results['companies']) && empty($results['employees']) && empty($results['assets'])): ?>
        <div class="content">
            <div class="no-results">
                <h3>No results found</h3>
                <p>Try searching for:</p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Company names</li>
                    <li>Employee names or departments</li>
                    <li>Asset names, tags, or serial numbers</li>
                    <li>Email addresses or phone numbers</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="content">
            <div class="no-results">
                <h3>🔍 Global Search</h3>
                <p>Search across all companies, employees, and assets</p>
                <p>Enter any name, email, phone, asset tag, or serial number above</p>
            </div>
        </div>
    <?php endif; ?>
    <script>
    function openCompanyEdit(companyId) {
        window.location.href = `companies.php#edit-${companyId}`;
        setTimeout(() => {
            if (window.openEditModal) {
                window.openEditModal(companyId);
            }
        }, 100);
    }

    function openEmployeeEdit(employeeId, companyId) {
        window.location.href = `employees.php?company_id=${companyId}#edit-${employeeId}`;
        setTimeout(() => {
            if (window.openEditModal) {
                window.openEditModal(employeeId);
            }
        }, 100);
    }

    function openAssetEdit(assetId) {
        window.location.href = `assets.php#edit-${assetId}`;
        setTimeout(() => {
            if (window.openEditModal) {
                window.openEditModal(assetId);
            }
        }, 100);
    }
    </script>
</body>
</html>