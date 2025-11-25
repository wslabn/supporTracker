<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php foreach ($this->styles as $style): ?>
        <link rel="stylesheet" href="<?= $style ?>">
    <?php endforeach; ?>
</head>
<body>
    <?php include __DIR__ . '/../navigation.php'; ?>
    
    <div class="container-fluid mt-4">
        <?= $this->content ?>
    </div>

    <!-- Modals -->
    <div id="modalContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Global functions available on all pages
    function toggleCustomerType() {
        const type = document.getElementById('customer_type')?.value;
        const companySection = document.getElementById('company_section');
        const individualSection = document.getElementById('individual_section');
        const companySelect = document.getElementById('company_id');
        const customerName = document.getElementById('customer_name');
        const deviceType = document.getElementById('device_type');
        
        if (type === 'company') {
            if (companySection) companySection.style.display = 'block';
            if (individualSection) individualSection.style.display = 'none';
            if (companySelect) companySelect.required = true;
            if (customerName) customerName.required = false;
            if (deviceType) deviceType.required = false;
        } else if (type === 'individual') {
            if (companySection) companySection.style.display = 'none';
            if (individualSection) individualSection.style.display = 'block';
            if (companySelect) companySelect.required = false;
            if (customerName) customerName.required = true;
            if (deviceType) deviceType.required = true;
        } else {
            if (companySection) companySection.style.display = 'none';
            if (individualSection) individualSection.style.display = 'none';
            if (companySelect) companySelect.required = false;
            if (customerName) customerName.required = false;
            if (deviceType) deviceType.required = false;
        }
    }
    
    function openWorkOrderModal() {
        const urlParams = new URLSearchParams(window.location.search);
        const companyId = urlParams.get('company_id');
        const assetId = urlParams.get('asset_id');
        let url = '/SupporTracker/workorders?action=add';
        if (companyId) url += `&company_id=${companyId}`;
        if (assetId) url += `&asset_id=${assetId}`;
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalContainer').innerHTML = html;
                const modal = new bootstrap.Modal(document.querySelector('#workorderModal'));
                modal.show();
                setupWorkOrderForm();
            });
    }
    
    function setupWorkOrderForm() {
        const form = document.getElementById('workorderForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('ajax', '1');
                formData.append('add_workorder', '1');
                
                fetch('/SupporTracker/workorders', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.querySelector('#workorderModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
            });
        }
    }
    
    function deleteWorkOrder(id) {
        if (confirm('Are you sure you want to delete this work order?')) {
            fetch('/SupporTracker/workorders', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `delete_workorder=1&workorder_id=${id}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
    }
    </script>
    
    <?php foreach ($this->scripts as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>