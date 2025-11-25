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
        </div>
        
        <div class="d-flex">
            <form method="GET" action="search" class="d-flex me-3">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Search..." style="width: 200px;">
                <button class="btn btn-outline-light btn-sm" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <a href="/SupporTracker/logout" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>