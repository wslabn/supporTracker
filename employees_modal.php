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

// Handle AJAX form submissions
if ($_POST && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['add_employee'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees (company_id, name, email, phone, department, title) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $company_id,
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['department'],
                $_POST['title']
            ]);
            echo json_encode(['success' => true, 'message' => 'Employee added successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    if (isset($_POST['update_employee'])) {
        try {
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
            echo json_encode(['success' => true, 'message' => 'Employee updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    exit;
}

// Get employee data for editing (AJAX)
if (isset($_GET['get_employee']) && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ? AND company_id = ?");
    $stmt->execute([$_GET['id'], $company_id]);
    $employee = $stmt->fetch();
    echo json_encode($employee);
    exit;
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
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?> - Employees</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .content { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007cba; text-decoration: none; }
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
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .actions a { margin-right: 10px; color: #007cba; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
        
        /* Alert Styles */
        .alert { padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php $page_title = 'Employees'; include 'includes/navigation.php'; ?>

    <div class="breadcrumb">
        <a href="companies.php">Companies</a> > 
        <a href="company_detail.php?id=<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></a> > 
        Employees
    </div>

    <div class="content">
        <div class="page-header">
            <h2>Employees - <?php echo htmlspecialchars($company['name']); ?></h2>
            <button onclick="openAddModal()" class="btn btn-success">+ Add Employee</button>
        </div>

        <div id="alert-container"></div>

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
                <td><strong><?php echo htmlspecialchars($employee['name']); ?></strong></td>
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
                <td class="actions">
                    <a href="#" onclick="openEditModal(<?php echo $employee['id']; ?>)">Edit</a>
                    <a href="assets.php?company_id=<?php echo $company_id; ?>&employee_id=<?php echo $employee['id']; ?>">View Assets</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Employee Modal -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Employee</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="employeeForm">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <input type="hidden" name="ajax" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Employee Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" id="department" name="department" placeholder="e.g., Accounting, Sales">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g., Manager, Receptionist">
                    </div>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" id="submitBtn" class="btn btn-success">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openAddModal() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Add Employee';
        document.getElementById('submitBtn').textContent = 'Add Employee';
        document.getElementById('employeeForm').reset();
        document.getElementById('employee_id').value = '';
        document.getElementById('employeeModal').style.display = 'block';
    }

    function openEditModal(employeeId) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Employee';
        document.getElementById('submitBtn').textContent = 'Update Employee';
        
        fetch(`?get_employee=1&id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('employee_id').value = data.id;
                document.getElementById('name').value = data.name || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('department').value = data.department || '';
                document.getElementById('title').value = data.title || '';
                document.getElementById('employeeModal').style.display = 'block';
            });
    }

    function closeModal() {
        document.getElementById('employeeModal').style.display = 'none';
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 3000);
    }

    // Handle form submission
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        if (isEditMode) {
            formData.append('update_employee', '1');
        } else {
            formData.append('add_employee', '1');
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
        const modal = document.getElementById('employeeModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html>