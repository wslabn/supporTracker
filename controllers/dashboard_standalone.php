<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /SupporTracker/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">SupportTracker</a>
                        <div class="navbar-nav ms-auto">
                            <a class="nav-link" href="/SupporTracker/tickets">Tickets</a>
                            <a class="nav-link" href="/SupporTracker/customers">Customers</a>
                            <a class="nav-link" href="/SupporTracker/invoices">Invoices</a>
                            <a class="nav-link" href="/SupporTracker/logout">Logout</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <h1>Dashboard</h1>
                <p>Welcome to SupportTracker!</p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>Quick Actions</h5>
                                <a href="/SupporTracker/tickets/create" class="btn btn-primary">New Ticket</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>