<?php
// Shared navigation with search - include on all pages
?>
<div class="header">
    <h1><?php echo APP_NAME; ?><?php if (isset($page_title)) echo ' - ' . $page_title; ?></h1>
    <div class="nav">
        <a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a>
        <a href="companies.php" <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'class="active"' : ''; ?>>Companies</a>
        <a href="assets.php" <?php echo basename($_SERVER['PHP_SELF']) == 'assets.php' ? 'class="active"' : ''; ?>>Assets</a>
        <a href="work_orders.php" <?php echo basename($_SERVER['PHP_SELF']) == 'work_orders.php' ? 'class="active"' : ''; ?>>Work Orders</a>
        <a href="invoices.php" <?php echo basename($_SERVER['PHP_SELF']) == 'invoices.php' ? 'class="active"' : ''; ?>>Invoices</a>
        <a href="payments.php" <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'class="active"' : ''; ?>>Payments</a>
        <a href="logout.php">Logout</a>
        <div style="float: right;">
            <form method="GET" action="search.php" style="display: inline;">
                <input type="text" name="q" placeholder="Search..." style="padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                <button type="submit" style="padding: 8px 12px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">🔍</button>
            </form>
        </div>
    </div>
</div>

<style>
.header { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
.nav { margin-bottom: 20px; }
.nav a { background: #007cba; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px; border-radius: 3px; }
.nav a:hover { background: #005a87; }
.nav a.active { background: #005a87; }
</style>