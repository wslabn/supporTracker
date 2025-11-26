<?php
// Shared navigation with search - include on all pages
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard"><?php echo APP_NAME; ?></a>
        
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="/SupporTracker/dashboard">Dashboard</a>
            <a class="nav-link" href="/SupporTracker/companies">Companies</a>
            <a class="nav-link" href="/SupporTracker/assets">Assets</a>
            <a class="nav-link" href="/SupporTracker/employees">Employees</a>
            <a class="nav-link" href="/SupporTracker/workorders">Work Orders</a>
            <a class="nav-link" href="/SupporTracker/projects">Projects</a>
            <a class="nav-link" href="/SupporTracker/parts">Parts</a>
            <a class="nav-link" href="/SupporTracker/invoices">Invoices</a>
            <a class="nav-link" href="/SupporTracker/settings">Settings</a>
        </div>
        
        <div class="d-flex">
            <select class="form-select form-select-sm me-3" id="locationSelector" onchange="changeLocation()" style="width: 200px;">
                <option value="">All Locations</option>
                <?php
                if (isset($pdo) && $pdo) {
                    try {
                        $stmt = $pdo->query("SELECT * FROM locations ORDER BY is_default DESC, name");
                        while ($loc = $stmt->fetch()) {
                            $selected = ($_SESSION['current_location'] ?? '') == $loc['id'] ? 'selected' : '';
                            echo "<option value='{$loc['id']}' $selected>{$loc['name']}</option>";
                        }
                    } catch (Exception $e) {
                        // Ignore database errors in navigation
                    }
                }
                ?>
            </select>
            <form method="GET" action="search" class="d-flex me-3">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Search..." style="width: 200px;">
                <button class="btn btn-outline-light btn-sm" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <a href="/SupporTracker/logout" class="btn btn-outline-light btn-sm">Logout</a>
        </div>

<script>
function changeLocation() {
    const locationId = document.getElementById('locationSelector').value;
    fetch('/SupporTracker/set_location', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'location_id=' + locationId
    }).then(() => location.reload());
}
</script>
    </div>
</nav>