<?php
require_once 'config.php';

$employee_id = $_GET['id'] ?? 0;

// Get employee details with company info
$stmt = $pdo->prepare("
    SELECT e.*, c.name as company_name 
    FROM employees e 
    LEFT JOIN companies c ON e.company_id = c.id 
    WHERE e.id = ?
");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: employees.php');
    exit;
}

// Get assets assigned to this employee
$stmt = $pdo->prepare("SELECT * FROM assets WHERE employee_id = ? ORDER BY name");
$stmt->execute([$employee_id]);
$assets = $stmt->fetchAll();

// Get credentials for employee's assets
$stmt = $pdo->prepare("
    SELECT c.*, a.name as asset_name 
    FROM credentials c 
    JOIN assets a ON c.asset_id = a.id 
    WHERE a.employee_id = ? 
    ORDER BY a.name, c.type
");
$stmt->execute([$employee_id]);
$credentials = $stmt->fetchAll();

// Get work orders for employee's assets
$stmt = $pdo->prepare("
    SELECT wo.*, a.name as asset_name 
    FROM work_orders wo 
    JOIN assets a ON wo.asset_id = a.id 
    WHERE a.employee_id = ? 
    ORDER BY wo.created_at DESC
");
$stmt->execute([$employee_id]);
$work_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee: <?= htmlspecialchars($employee['name']) ?> - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-user"></i> Employee Details</h5>
                        <button class="btn btn-sm btn-primary" onclick="editEmployee(<?= $employee['id'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr><td><strong>Name:</strong></td><td><?= htmlspecialchars($employee['name']) ?></td></tr>
                            <tr><td><strong>Company:</strong></td><td>
                                <a href="company_detail.php?id=<?= $employee['company_id'] ?>"><?= htmlspecialchars($employee['company_name']) ?></a>
                            </td></tr>
                            <tr><td><strong>Email:</strong></td><td>
                                <?php if ($employee['email']): ?>
                                    <a href="mailto:<?= htmlspecialchars($employee['email']) ?>"><?= htmlspecialchars($employee['email']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </td></tr>
                            <tr><td><strong>Phone:</strong></td><td>
                                <?php if ($employee['phone']): ?>
                                    <a href="tel:<?= htmlspecialchars($employee['phone']) ?>"><?= htmlspecialchars($employee['phone']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </td></tr>
                            <tr><td><strong>Position:</strong></td><td><?= htmlspecialchars($employee['position'] ?? 'Not specified') ?></td></tr>
                            <tr><td><strong>Department:</strong></td><td><?= htmlspecialchars($employee['department'] ?? 'Not specified') ?></td></tr>
                            <tr><td><strong>Status:</strong></td><td>
                                <span class="badge bg-<?= $employee['status'] == 'Active' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($employee['status']) ?>
                                </span>
                            </td></tr>
                        </table>
                        <?php if ($employee['notes']): ?>
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <div class="border p-2 mt-1"><?= nl2br(htmlspecialchars($employee['notes'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <ul class="nav nav-tabs" id="employeeTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#assets">
                            <i class="fas fa-desktop"></i> Assets (<?= count($assets) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#credentials">
                            <i class="fas fa-key"></i> Credentials (<?= count($credentials) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#workorders">
                            <i class="fas fa-wrench"></i> Work Orders (<?= count($work_orders) ?>)
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="assets">
                        <div class="card">
                            <div class="card-header">
                                <h6>Assigned Assets</h6>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($assets): ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Tag</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assets as $asset): ?>
                                        <tr>
                                            <td><a href="asset_detail.php?id=<?= $asset['id'] ?>"><?= htmlspecialchars($asset['name']) ?></a></td>
                                            <td><?= htmlspecialchars($asset['asset_tag']) ?></td>
                                            <td><?= htmlspecialchars($asset['type']) ?></td>
                                            <td><?= htmlspecialchars($asset['location']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $asset['status'] == 'Active' ? 'success' : 'secondary' ?>">
                                                    <?= htmlspecialchars($asset['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="editAsset(<?= $asset['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p class="text-muted p-3">No assets assigned to this employee.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="credentials">
                        <div class="card">
                            <div class="card-header">
                                <h6>Asset Credentials</h6>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($credentials): ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Asset</th>
                                            <th>Type</th>
                                            <th>Username</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($credentials as $cred): ?>
                                        <tr>
                                            <td><a href="asset_detail.php?id=<?= $cred['asset_id'] ?>"><?= htmlspecialchars($cred['asset_name']) ?></a></td>
                                            <td><?= htmlspecialchars($cred['type']) ?></td>
                                            <td><?= htmlspecialchars($cred['username']) ?></td>
                                            <td><?= htmlspecialchars($cred['description']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="editCredential(<?= $cred['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p class="text-muted p-3">No credentials found for this employee's assets.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="workorders">
                        <div class="card">
                            <div class="card-header">
                                <h6>Work Orders</h6>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($work_orders): ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Asset</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($work_orders as $wo): ?>
                                        <tr>
                                            <td>#<?= $wo['id'] ?></td>
                                            <td><a href="asset_detail.php?id=<?= $wo['asset_id'] ?>"><?= htmlspecialchars($wo['asset_name']) ?></a></td>
                                            <td><?= htmlspecialchars($wo['title']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusColor($wo['status']) ?>">
                                                    <?= htmlspecialchars($wo['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= getPriorityColor($wo['priority']) ?>">
                                                    <?= htmlspecialchars($wo['priority']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($wo['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="editWorkOrder(<?= $wo['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p class="text-muted p-3">No work orders found for this employee's assets.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div id="modalContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editEmployee(id) {
            fetch(`employees.php?action=edit&id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    new bootstrap.Modal(document.querySelector('#employeeModal')).show();
                });
        }

        function editAsset(id) {
            fetch(`assets.php?action=edit&id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    new bootstrap.Modal(document.querySelector('#assetModal')).show();
                });
        }

        function editCredential(id) {
            fetch(`credentials.php?action=edit&id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    new bootstrap.Modal(document.querySelector('#credentialModal')).show();
                });
        }

        function editWorkOrder(id) {
            alert('Work order functionality coming soon');
        }
    </script>
</body>
</html>

<?php
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'open': return 'primary';
        case 'in progress': return 'warning';
        case 'completed': return 'success';
        case 'closed': return 'secondary';
        default: return 'secondary';
    }
}

function getPriorityColor($priority) {
    switch (strtolower($priority)) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'danger';
        default: return 'secondary';
    }
}
?>