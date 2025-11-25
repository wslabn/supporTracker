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
    <?php foreach ($this->scripts as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>