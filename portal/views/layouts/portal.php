<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .status-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
    
    <script>
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        document.documentElement.setAttribute('data-bs-theme', mediaQuery.matches ? 'dark' : 'light');
        mediaQuery.addEventListener('change', (e) => {
            document.documentElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
        });
    </script>
</head>
<body>
    <div class="container mt-4">
        <?= $content ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('PORTAL LAYOUT LOADED:', window.location.href);
        
        document.addEventListener('click', function(e) {
            console.log('CLICKED:', e.target.tagName, e.target.textContent?.trim());
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('PORTAL DOM READY');
            const forms = document.querySelectorAll('form');
            console.log('FORMS FOUND:', forms.length);
            
            forms.forEach(function(form, index) {
                form.addEventListener('submit', function(e) {
                    const formData = new FormData(this);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        data[key] = value;
                    }
                    
                    // Only prevent credential forms for debugging
                    if (data.add_credential) {
                        e.preventDefault();
                        console.log('CREDENTIAL FORM PREVENTED:', data);
                        return false;
                    } else {
                        console.log('FORM SUBMITTING:', index, data);
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        data[key] = value;
                    }
                    
                    console.log('Form submission prevented');
                    console.log('Form data being sent:', data);
                    console.log('Form action:', this.action || 'current page');
                    console.log('Form method:', this.method);
                    
                    return false;
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Debug script loaded');
            const forms = document.querySelectorAll('form');
            console.log('Found forms:', forms.length);
            
            forms.forEach(function(form, index) {
                console.log('Form', index, ':', form);
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        data[key] = value;
                    }
                    
                    console.log('Form submission prevented');
                    console.log('Form data being sent:', data);
                    console.log('Form action:', this.action || 'current page');
                    console.log('Form method:', this.method);
                    
                    return false;
                });
            });
        });
    </script>
</body>
</html>