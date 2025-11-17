<?php
require_once 'config.php';

$asset_id = $_GET['id'] ?? 0;

// Get asset details with company info
$stmt = $pdo->prepare("
    SELECT a.*, c.name as company_name, e.name as employee_name 
    FROM assets a 
    LEFT JOIN companies c ON a.company_id = c.id 
    LEFT JOIN employees e ON a.employee_id = e.id 
    WHERE a.id = ?
");
$stmt->execute([$asset_id]);
$asset = $stmt->fetch();

if (!$asset) {
    header('Location: assets.php');
    exit;
}

// Get credentials for this asset
$stmt = $pdo->prepare("SELECT * FROM credentials WHERE asset_id = ? ORDER BY created_at DESC");
$stmt->execute([$asset_id]);
$credentials = $stmt->fetchAll();

// Get work orders for this asset
$stmt = $pdo->prepare("
    SELECT wo.*, c.name as company_name 
    FROM work_orders wo 
    LEFT JOIN companies c ON wo.company_id = c.id 
    WHERE wo.asset_id = ? 
    ORDER BY wo.created_at DESC
");
$stmt->execute([$asset_id]);
$work_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset: <?= htmlspecialchars($asset['name']) ?> - SupportTracker</title>
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
                        <h5><i class="fas fa-desktop"></i> Asset Details</h5>
                        <button class="btn btn-sm btn-primary" onclick="editAsset(<?= $asset['id'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr><td><strong>Name:</strong></td><td><?= htmlspecialchars($asset['name']) ?></td></tr>
                            <tr><td><strong>Tag:</strong></td><td><?= htmlspecialchars($asset['asset_tag']) ?></td></tr>
                            <tr><td><strong>Type:</strong></td><td><?= htmlspecialchars($asset['type']) ?></td></tr>
                            <tr><td><strong>Company:</strong></td><td>
                                <a href="company_detail.php?id=<?= $asset['company_id'] ?>"><?= htmlspecialchars($asset['company_name']) ?></a>
                            </td></tr>
                            <tr><td><strong>Employee:</strong></td><td><?= htmlspecialchars($asset['employee_name'] ?? 'Unassigned') ?></td></tr>
                            <tr><td><strong>Location:</strong></td><td><?= htmlspecialchars($asset['location']) ?></td></tr>
                            <tr><td><strong>Serial:</strong></td><td><?= htmlspecialchars($asset['serial_number']) ?></td></tr>
                            <tr><td><strong>Model:</strong></td><td><?= htmlspecialchars($asset['model']) ?></td></tr>
                            <tr><td><strong>Status:</strong></td><td>
                                <span class="badge bg-<?= $asset['status'] == 'Active' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($asset['status']) ?>
                                </span>
                            </td></tr>
                        </table>
                        <?php if ($asset['notes']): ?>
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <div class="border p-2 mt-1"><?= nl2br(htmlspecialchars($asset['notes'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <ul class="nav nav-tabs" id="assetTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#credentials">
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
                    <div class="tab-pane fade show active" id="credentials">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h6>Credentials</h6>
                                <button class="btn btn-sm btn-success" onclick="addCredential(<?= $asset_id ?>)">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($credentials): ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Username</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($credentials as $cred): ?>
                                        <tr>
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
                                <p class="text-muted p-3">No credentials found for this asset.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="workorders">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h6>Work Orders</h6>
                                <button class="btn btn-sm btn-success" onclick="addWorkOrder(<?= $asset_id ?>)">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($work_orders): ?>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
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
                                <p class="text-muted p-3">No work orders found for this asset.</p>
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
        function editAsset(id) {
            fetch(`assets.php?action=edit&id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    new bootstrap.Modal(document.querySelector('#assetModal')).show();
                });
        }

        function addCredential(assetId) {
            fetch(`credentials.php?action=add&asset_id=${assetId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalContainer').innerHTML = html;
                    new bootstrap.Modal(document.querySelector('#credentialModal')).show();
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

        function addWorkOrder(assetId) {
            // Placeholder for work order functionality
            alert('Work order functionality coming soon');
        }

        function editWorkOrder(id) {
            // Placeholder for work order functionality
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