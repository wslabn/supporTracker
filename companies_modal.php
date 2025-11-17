<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('index.php');
}

// Handle AJAX form submissions
if ($_POST && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['add_company'])) {
        try {
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
            echo json_encode(['success' => true, 'message' => 'Company added successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['update_company'])) {
        try {
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
            echo json_encode(['success' => true, 'message' => 'Company updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    exit;
}

// Get company data for editing (AJAX)
if (isset($_GET['get_company']) && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $company = $stmt->fetch();
    echo json_encode($company);
    exit;
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
        .content { background: white; padding: 20px; border-radius: 5px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 0; border-radius: 5px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; }
        .modal-body { padding: 20px; }
        .close { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        
        /* Form Styles */
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        .form-group textarea { height: 60px; }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; }
        .currency { text-align: right; }
        .actions a { margin-right: 10px; color: #007cba; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
        
        /* Alert Styles */
        .alert { padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php $page_title = 'Companies'; include 'includes/navigation.php'; ?>

    <div class="content">
        <div class="page-header">
            <h2>Companies</h2>
            <button onclick="openAddModal()" class="btn btn-success">+ Add Company</button>
        </div>

        <div id="alert-container"></div>

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
                <td class="actions">
                    <a href="#" onclick="openEditModal(<?php echo $company['id']; ?>)">Edit</a>
                    <a href="company_detail.php?id=<?php echo $company['id']; ?>">View</a>
                    <a href="employees.php?company_id=<?php echo $company['id']; ?>">Employees</a>
                    <a href="assets.php?company_id=<?php echo $company['id']; ?>">Assets</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Company Modal -->
    <div id="companyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Company</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="companyForm">
                    <input type="hidden" id="company_id" name="company_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Monthly Rate</label>
                            <input type="number" id="monthly_rate" name="monthly_rate" step="0.01" placeholder="125.00">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="address" name="address"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Billing Contact</label>
                            <input type="text" id="billing_contact" name="billing_contact">
                        </div>
                        <div class="form-group">
                            <label>Technical Contact</label>
                            <input type="text" id="technical_contact" name="technical_contact">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="notes" name="notes" placeholder="Contract details, special pricing notes, etc."></textarea>
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" id="submitBtn" class="btn btn-success">Add Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openAddModal() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Add Company';
        document.getElementById('submitBtn').textContent = 'Add Company';
        document.getElementById('companyForm').reset();
        document.getElementById('company_id').value = '';
        document.getElementById('companyModal').style.display = 'block';
    }

    function openEditModal(companyId) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Company';
        document.getElementById('submitBtn').textContent = 'Update Company';
        
        // Fetch company data
        fetch(`?get_company=1&id=${companyId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('company_id').value = data.id;
                document.getElementById('name').value = data.name || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('address').value = data.address || '';
                document.getElementById('monthly_rate').value = data.monthly_rate || '';
                document.getElementById('billing_contact').value = data.billing_contact || '';
                document.getElementById('technical_contact').value = data.technical_contact || '';
                document.getElementById('notes').value = data.notes || '';
                document.getElementById('companyModal').style.display = 'block';
            });
    }

    function closeModal() {
        document.getElementById('companyModal').style.display = 'none';
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 3000);
    }

    // Handle form submission
    document.getElementById('companyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        if (isEditMode) {
            formData.append('update_company', '1');
        } else {
            formData.append('add_company', '1');
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
        const modal = document.getElementById('companyModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html>