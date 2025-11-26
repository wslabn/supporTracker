<?php
require_once 'config.php';

if ($_POST) {
    if (isset($_POST['create_category'])) {
        $stmt = $pdo->prepare("INSERT INTO asset_categories (name, description, icon, color) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['icon'],
            $_POST['color']
        ]);
        header('Location: /SupporTracker/asset-categories');
        exit;
    }
    
    if (isset($_POST['update_category'])) {
        $stmt = $pdo->prepare("UPDATE asset_categories SET name = ?, description = ?, icon = ?, color = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['icon'],
            $_POST['color'],
            $_POST['id']
        ]);
        header('Location: /SupporTracker/asset-categories');
        exit;
    }
}

// Get categories
$categories = $pdo->query("
    SELECT ac.*, 
           COUNT(a.id) as asset_count
    FROM asset_categories ac
    LEFT JOIN assets a ON ac.id = a.category_id
    GROUP BY ac.id
    ORDER BY ac.name
")->fetchAll();

$headerActions = '
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
    <i class="bi bi-plus-lg me-1"></i>
    New Category
</button>';

renderModernPage(
    'Asset Categories - SupportTracker',
    'Asset Categories',
    'asset-categories.php',
    compact('categories'),
    $headerActions
);
?>