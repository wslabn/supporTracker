<?php
// Simple redirect system to use new templating
$page = $_GET['page'] ?? 'dashboard';
$id = $_GET['id'] ?? null;

switch($page) {
    case 'asset':
        if ($id) {
            header("Location: asset?id=$id");
        } else {
            header("Location: assets");
        }
        break;
    case 'company':
        if ($id) {
            header("Location: company?id=$id");
        } else {
            header("Location: companies");
        }
        break;
    case 'employee':
        if ($id) {
            header("Location: employee?id=$id");
        } else {
            header("Location: employees");
        }
        break;
    default:
        header("Location: $page");
}
exit;
?>