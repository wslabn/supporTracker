<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['create_project'])) {
        $stmt = $pdo->prepare("INSERT INTO projects (customer_id, name, description, status, start_date, end_date, budget) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['status'],
            $_POST['start_date'] ?: null,
            $_POST['end_date'] ?: null,
            $_POST['budget'] ?: null
        ]);
        header('Location: /SupporTracker/projects');
        exit;
    }
}

// Get projects
$projects = $pdo->query("
    SELECT p.*, c.name as customer_name,
           COUNT(DISTINCT pt.ticket_id) as ticket_count
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.id
    LEFT JOIN project_tickets pt ON p.id = pt.project_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
")->fetchAll();

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();
$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Project
</button>';

renderModernPage(
    'Projects - SupportTracker',
    'Projects',
    'projects.php',
    compact('projects', 'customers', 'users'),
    $headerActions
);
?>