<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

$company_id = $_GET['id'] ?? null;
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

// Get company stats
$stats = [];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM employees WHERE company_id = ?");
$stmt->execute([$company_id]);
$stats['employees'] = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assets WHERE company_id = ?");
$stmt->execute([$company_id]);
$stats['assets'] = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_orders WHERE company_id = ? AND status IN ('new', 'in_progress')");
$stmt->execute([$company_id]);
$stats['open_tickets'] = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT SUM(balance) as total FROM invoices WHERE company_id = ? AND status IN ('sent', 'partial', 'overdue')");
$stmt->execute([$company_id]);
$stats['outstanding'] = $stmt->fetch()['total'] ?: 0;

// Get recent activity
$stmt = $pdo->prepare("
    SELECT 'work_order' as type, id, title as description, status, created_at 
    FROM work_orders WHERE company_id = ? 
    UNION ALL
    SELECT 'invoice' as type, id, CONCAT('Invoice #', invoice_number, ' - ', total_amount) as description, status, created_at 
    FROM invoices WHERE company_id = ?
    ORDER BY created_at DESC LIMIT 10
");
$stmt->execute([$company_id, $company_id]);
$recent_activity = $stmt->fetchAll();

$active_tab = $_GET['tab'] ?? 'overview';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - <?php echo htmlspecialchars($company['name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .nav { margin-bottom: 20px; }
        .nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
        .nav a:hover { background: #005a87; }
        .company-header { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .company-title { font-size: 24px; font-weight: bold; color: #007cba; margin-bottom: 10px; }
        .company-info { display: flex; gap: 40px; }
        .company-details { flex: 2; }
        .company-stats { flex: 1; }
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007cba; }
        .stat-label { color: #666; font-size: 14px; }
        .tabs { background: white; border-radius: 5px; margin-bottom: 20px; }
        .tab-nav { display: flex; border-bottom: 1px solid #ddd; }
        .tab-nav a { padding: 15px 20px; text-decoration: none; color: #666; border-bottom: 3px solid transparent; }
        .tab-nav a.active { color: #007cba; border-bottom-color: #007cba; background: #f8f9fa; }
        .tab-nav a:hover { background: #f8f9fa; }
        .tab-content { padding: 20px; }
        .quick-actions { margin-bottom: 20px; }
        .btn { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; margin-right: 10px; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .activity-item { padding: 10px; border-left: 4px solid #007cba; margin-bottom: 10px; background: #f8f9fa; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo APP_NAME; ?></h1>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="companies.php">Companies</a>
            <a href="assets.php">Assets</a>
            <a href="work_orders.php">Work Orders</a>
            <a href="invoices.php">Invoices</a>
            <a href="payments.php">Payments</a>
            <a href="logout.php">Logout</a>
            <div style="float: right;">
                <form method="GET" action="search.php" style="display: inline;">
                    <input type="text" name="q" placeholder="Search..." style="padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <button type="submit" style="padding: 8px 12px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">🔍</button>
                </form>
            </div>
        </div>
    </div>

    <div class="breadcrumb">
        <a href="companies.php">Companies</a> > <?php echo htmlspecialchars($company['name']); ?>
    </div>

    <div class="company-header">
        <div class="company-title"><?php echo htmlspecialchars($company['name']); ?></div>
        <div class="company-info">
            <div class="company-details">
                <p><strong>Monthly Rate:</strong> <?php echo formatCurrency($company['monthly_rate']); ?></p>
                <?php if ($company['email']): ?>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($company['email']); ?></p>
                <?php endif; ?>
                <?php if ($company['phone']): ?>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($company['phone']); ?></p>
                <?php endif; ?>
                <?php if ($company['address']): ?>
                    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($company['address'])); ?></p>
                <?php endif; ?>
            </div>
            <div class="company-stats">
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['employees']; ?></div>
                        <div class="stat-label">Employees</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['assets']; ?></div>
                        <div class="stat-label">Assets</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['open_tickets']; ?></div>
                        <div class="stat-label">Open Tickets</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo formatCurrency($stats['outstanding']); ?></div>
                        <div class="stat-label">Outstanding</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs">
        <div class="tab-nav">
            <a href="?id=<?php echo $company_id; ?>&tab=overview" class="<?php echo $active_tab == 'overview' ? 'active' : ''; ?>">Overview</a>
            <a href="?id=<?php echo $company_id; ?>&tab=employees" class="<?php echo $active_tab == 'employees' ? 'active' : ''; ?>">Employees (<?php echo $stats['employees']; ?>)</a>
            <a href="?id=<?php echo $company_id; ?>&tab=assets" class="<?php echo $active_tab == 'assets' ? 'active' : ''; ?>">Assets (<?php echo $stats['assets']; ?>)</a>
            <a href="?id=<?php echo $company_id; ?>&tab=tickets" class="<?php echo $active_tab == 'tickets' ? 'active' : ''; ?>">Work Orders</a>
            <a href="?id=<?php echo $company_id; ?>&tab=billing" class="<?php echo $active_tab == 'billing' ? 'active' : ''; ?>">Billing</a>
        </div>
        
        <div class="tab-content">
            <?php if ($active_tab == 'overview'): ?>
                <div class="quick-actions">
                    <button onclick="openEmployeeModal()" class="btn btn-success">+ Add Employee</button>
                    <button onclick="openAssetModal()" class="btn btn-success">+ Add Asset</button>
                    <a href="work_orders.php?company_id=<?php echo $company_id; ?>" class="btn btn-success">+ Create Work Order</a>
                    <button onclick="openCompanyModal()" class="btn">Edit Company</button>
                </div>
                
                <h3>Recent Activity</h3>
                <?php if (empty($recent_activity)): ?>
                    <p>No recent activity for this company.</p>
                <?php else: ?>
                    <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-item">
                        <strong><?php echo ucfirst(str_replace('_', ' ', $activity['type'])); ?>:</strong>
                        <?php echo htmlspecialchars($activity['description']); ?>
                        <small style="float: right; color: #666;"><?php echo formatDate($activity['created_at']); ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
            <?php elseif ($active_tab == 'employees'): ?>
                <div class="quick-actions">
                    <button onclick="openEmployeeModal()" class="btn btn-success">+ Add Employee</button>
                </div>
                
                <?php
                // Get employees for this company
                $stmt = $pdo->prepare("SELECT e.*, COUNT(a.id) as asset_count FROM employees e LEFT JOIN assets a ON e.id = a.employee_id WHERE e.company_id = ? GROUP BY e.id ORDER BY e.name");
                $stmt->execute([$company_id]);
                $employees = $stmt->fetchAll();
                ?>
                
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
                            <a href="#" onclick="openEmployeeEditModal(<?php echo $employee['id']; ?>); return false;">Edit</a> |
                            <a href="assets.php?company_id=<?php echo $company_id; ?>&employee_id=<?php echo $employee['id']; ?>">View Assets</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
            <?php elseif ($active_tab == 'assets'): ?>
                <div class="quick-actions">
                    <button onclick="openAssetModal()" class="btn btn-success">+ Add Asset</button>
                </div>
                
                <?php
                // Get assets for this company
                $stmt = $pdo->prepare("SELECT a.*, cat.name as category_name, e.name as employee_name, COUNT(ac.id) as credential_count FROM assets a LEFT JOIN asset_categories cat ON a.category_id = cat.id LEFT JOIN employees e ON a.employee_id = e.id LEFT JOIN asset_credentials ac ON a.id = ac.asset_id WHERE a.company_id = ? GROUP BY a.id ORDER BY a.name");
                $stmt->execute([$company_id]);
                $assets = $stmt->fetchAll();
                ?>
                
                <table>
                    <tr>
                        <th>Asset</th>
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
                            <a href="#" onclick="openAssetEditModal(<?php echo $asset['id']; ?>); return false;">Edit</a> |
                            <a href="credentials.php?asset_id=<?php echo $asset['id']; ?>" style="color: #dc3545;">🔒 Credentials</a>
                            <?php if ($asset['credential_count'] > 0): ?>
                                <small>(<?php echo $asset['credential_count']; ?>)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
            <?php elseif ($active_tab == 'tickets'): ?>
                <div class="quick-actions">
                    <a href="work_orders.php?company_id=<?php echo $company_id; ?>" class="btn btn-success">+ Create Work Order</a>
                </div>
                <p>Work orders functionality coming soon...</p>
                
            <?php elseif ($active_tab == 'billing'): ?>
                <div class="quick-actions">
                    <a href="invoices.php?company_id=<?php echo $company_id; ?>" class="btn btn-success">+ Create Invoice</a>
                </div>
                <p>Billing functionality coming soon...</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Employee Modal -->
    <div id="employeeModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 0; border-radius: 5px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="employeeModalTitle">Add Employee</h3>
                <span class="close" onclick="closeEmployeeModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="employeeForm">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Employee Name *</label>
                            <input type="text" id="emp_name" name="name" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                            <input type="email" id="emp_email" name="email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                            <input type="text" id="emp_phone" name="phone" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Department</label>
                            <input type="text" id="emp_department" name="department" placeholder="e.g., Accounting, Sales" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Job Title</label>
                        <input type="text" id="emp_title" name="title" placeholder="e.g., Manager, Receptionist" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                    </div>
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeEmployeeModal()" class="btn" style="background: #6c757d; margin-right: 10px;">Cancel</button>
                        <button type="submit" id="employeeSubmitBtn" class="btn" style="background: #28a745;">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Asset Modal -->
    <div id="assetModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 2% auto; padding: 0; border-radius: 5px; width: 90%; max-width: 800px; max-height: 95vh; overflow-y: auto;">
            <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="assetModalTitle">Add Asset</h3>
                <span class="close" onclick="closeAssetModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="assetForm">
                    <input type="hidden" id="asset_id" name="asset_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Category</label>
                            <select id="asset_category_id" name="category_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                                <option value="">Select Category</option>
                                <?php
                                $stmt = $pdo->query("SELECT id, name FROM asset_categories ORDER BY name");
                                $categories = $stmt->fetchAll();
                                foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Asset Tag</label>
                            <input type="text" id="asset_tag" name="asset_tag" placeholder="Leave blank for auto: AST-0001" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Asset Name *</label>
                        <input type="text" id="asset_name" name="name" required placeholder="e.g., Reception Desk Computer" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                    </div>
                    
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Make</label>
                            <input type="text" id="asset_make" name="make" placeholder="e.g., Dell, HP, Cisco" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Model</label>
                            <input type="text" id="asset_model" name="model" placeholder="e.g., OptiPlex 7090" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Serial Number</label>
                            <input type="text" id="asset_serial" name="serial_number" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Location</label>
                            <input type="text" id="asset_location" name="location" placeholder="e.g., Front Desk, Server Room" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Assigned To (Employee)</label>
                        <select id="asset_employee_id" name="employee_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                            <option value="">Unassigned</option>
                            <option value="new">+ Add New Employee</option>
                        </select>
                        
                        <div id="new_employee_form" style="display: none; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 3px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Employee Name:</label>
                            <input type="text" id="new_employee_name" name="new_employee_name" placeholder="Enter employee name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Notes</label>
                        <textarea id="asset_notes" name="notes" placeholder="Configuration details, warranty info, etc." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; height: 60px;"></textarea>
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeAssetModal()" class="btn" style="background: #6c757d; margin-right: 10px;">Cancel</button>
                        <button type="submit" id="assetSubmitBtn" class="btn" style="background: #28a745;">Add Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEmployeeEditMode = false;
    let isAssetEditMode = false;

    function openEmployeeModal() {
        isEmployeeEditMode = false;
        document.getElementById('employeeModalTitle').textContent = 'Add Employee';
        document.getElementById('employeeSubmitBtn').textContent = 'Add Employee';
        document.getElementById('employeeForm').reset();
        document.getElementById('employee_id').value = '';
        document.getElementById('employeeModal').style.display = 'block';
    }

    function closeEmployeeModal() {
        document.getElementById('employeeModal').style.display = 'none';
        document.getElementById('employeeForm').reset();
    }

    function openAssetModal() {
        isAssetEditMode = false;
        document.getElementById('assetModalTitle').textContent = 'Add Asset';
        document.getElementById('assetSubmitBtn').textContent = 'Add Asset';
        document.getElementById('assetForm').reset();
        document.getElementById('asset_id').value = '';
        
        // Load employees for this company
        loadAssetEmployees();
        
        document.getElementById('assetModal').style.display = 'block';
    }

    function closeAssetModal() {
        document.getElementById('assetModal').style.display = 'none';
        document.getElementById('assetForm').reset();
        document.getElementById('new_employee_form').style.display = 'none';
    }

    function loadAssetEmployees() {
        const employeeSelect = document.getElementById('asset_employee_id');
        employeeSelect.innerHTML = '<option value="">Unassigned</option><option value="new">+ Add New Employee</option>';
        
        fetch(`employees.php?get_employees=1&company_id=<?php echo $company_id; ?>`)
            .then(response => response.json())
            .then(employees => {
                employees.forEach(employee => {
                    const option = document.createElement('option');
                    option.value = employee.id;
                    option.textContent = employee.name;
                    employeeSelect.insertBefore(option, employeeSelect.lastElementChild);
                });
            })
            .catch(() => {});
    }

    function openCompanyModal() {
        window.location.href = 'companies.php?edit=<?php echo $company_id; ?>';
    }

    // Handle employee dropdown change
    document.getElementById('asset_employee_id').addEventListener('change', function() {
        const newForm = document.getElementById('new_employee_form');
        if (this.value === 'new') {
            newForm.style.display = 'block';
        } else {
            newForm.style.display = 'none';
        }
    });

    // Handle employee form submission
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        if (isEmployeeEditMode) {
            formData.append('update_employee', '1');
        } else {
            formData.append('add_employee', '1');
        }
        
        fetch(`employees.php?company_id=<?php echo $company_id; ?>`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEmployeeModal();
                location.reload();
            }
        })
        .catch(() => {
            closeEmployeeModal();
            location.reload();
        });
    });

    // Handle asset form submission
    document.getElementById('assetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('company_id', '<?php echo $company_id; ?>');
        
        // Handle new employee creation
        if (document.getElementById('asset_employee_id').value === 'new' && document.getElementById('new_employee_name').value) {
            formData.append('add_new_employee', '1');
        }
        
        if (isAssetEditMode) {
            formData.append('update_asset', '1');
        } else {
            formData.append('add_asset', '1');
        }
        
        fetch('assets.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAssetModal();
                location.reload();
            }
        })
        .catch(() => {
            closeAssetModal();
            location.reload();
        });
    });

    function openEmployeeEditModal(employeeId) {
        isEmployeeEditMode = true;
        document.getElementById('employeeModalTitle').textContent = 'Edit Employee';
        document.getElementById('employeeSubmitBtn').textContent = 'Update Employee';
        
        fetch(`employees.php?get_employee=1&id=${employeeId}&company_id=<?php echo $company_id; ?>`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('employee_id').value = data.id;
                document.getElementById('emp_name').value = data.name || '';
                document.getElementById('emp_email').value = data.email || '';
                document.getElementById('emp_phone').value = data.phone || '';
                document.getElementById('emp_department').value = data.department || '';
                document.getElementById('emp_title').value = data.title || '';
                document.getElementById('employeeModal').style.display = 'block';
            });
    }

    function openAssetEditModal(assetId) {
        isAssetEditMode = true;
        document.getElementById('assetModalTitle').textContent = 'Edit Asset';
        document.getElementById('assetSubmitBtn').textContent = 'Update Asset';
        
        fetch(`assets.php?get_asset=1&id=${assetId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('asset_id').value = data.id;
                document.getElementById('asset_category_id').value = data.category_id || '';
                document.getElementById('asset_name').value = data.name || '';
                document.getElementById('asset_tag').value = data.asset_tag || '';
                document.getElementById('asset_make').value = data.make || '';
                document.getElementById('asset_model').value = data.model || '';
                document.getElementById('asset_serial').value = data.serial_number || '';
                document.getElementById('asset_location').value = data.location || '';
                document.getElementById('asset_notes').value = data.notes || '';
                
                loadAssetEmployees().then(() => {
                    document.getElementById('asset_employee_id').value = data.employee_id || '';
                });
                
                document.getElementById('assetModal').style.display = 'block';
            });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html>