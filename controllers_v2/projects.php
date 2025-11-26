<?php
require_once 'config_v2.php';

if ($_POST) {
    if (isset($_POST['create_project'])) {
        $stmt = $pdo->prepare("INSERT INTO projects (customer_id, name, description, status, priority, start_date, due_date, budget, project_manager_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            $_POST['start_date'] ?: null,
            $_POST['due_date'] ?: null,
            $_POST['budget'] ?: null,
            $_POST['project_manager_id'] ?: null
        ]);
        header('Location: /SupporTracker/projects');
        exit;
    }
}

// Get projects
$projects = $pdo->query("
    SELECT p.*, c.name as customer_name, t.name as manager_name,
           COUNT(DISTINCT tickets.id) as ticket_count
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.id
    LEFT JOIN technicians t ON p.project_manager_id = t.id
    LEFT JOIN tickets ON p.id = tickets.project_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
")->fetchAll();

$customers = $pdo->query("SELECT id, name FROM customers WHERE status = 'active' ORDER BY name")->fetchAll();
$technicians = $pdo->query("SELECT id, name FROM technicians WHERE status = 'active' ORDER BY name")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Project
</button>';

renderModernPage(
    'Projects - SupportTracker',
    'Projects',
    'projects.php',
    compact('projects', 'customers', 'technicians'),
    $headerActions
);
?>