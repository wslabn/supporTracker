<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['create_category'])) {
        $stmt = $pdo->prepare("INSERT INTO service_categories (name, type, description, icon, color, default_priority, sla_hours, billable_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['type'],
            $_POST['description'],
            $_POST['icon'],
            $_POST['color'],
            $_POST['default_priority'],
            $_POST['sla_hours'],
            isset($_POST['billable_default']) ? 1 : 0
        ]);
        header('Location: /SupporTracker/service-categories');
        exit;
    }
    
    if (isset($_POST['update_category'])) {
        $stmt = $pdo->prepare("UPDATE service_categories SET name = ?, type = ?, description = ?, icon = ?, color = ?, default_priority = ?, sla_hours = ?, billable_default = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['type'],
            $_POST['description'],
            $_POST['icon'],
            $_POST['color'],
            $_POST['default_priority'],
            $_POST['sla_hours'],
            isset($_POST['billable_default']) ? 1 : 0,
            $_POST['id']
        ]);
        header('Location: /SupporTracker/service-categories');
        exit;
    }
}

// Get categories with usage counts
$categories = $pdo->query("
    SELECT sc.*, 
           COUNT(DISTINCT t.id) as ticket_count,
           COUNT(DISTINCT p.id) as project_count
    FROM service_categories sc
    LEFT JOIN tickets t ON sc.id = t.service_category_id
    LEFT JOIN projects p ON sc.id = p.service_category_id
    GROUP BY sc.id
    ORDER BY sc.name
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Service Category
</button>';

renderModernPage(
    'Service Categories - SupportTracker',
    'Service Categories',
    'service-categories.php',
    compact('categories'),
    $headerActions
);
?>