<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($this->title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --st-primary: #0d6efd;
            --st-secondary: #6c757d;
            --st-success: #198754;
            --st-warning: #ffc107;
            --st-danger: #dc3545;
            --st-info: #0dcaf0;
        }
        
        [data-bs-theme="dark"] {
            --st-bg-subtle: #212529;
            --st-border-color: #495057;
        }
        
        [data-bs-theme="light"] {
            --st-bg-subtle: #f8f9fa;
            --st-border-color: #dee2e6;
        }
        
        .sidebar {
            min-height: 100vh;
            background: var(--st-bg-subtle);
            border-right: 1px solid var(--st-border-color);
        }
        
        .main-content {
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .nav-link {
            border-radius: 0.375rem;
            margin: 0.125rem 0;
        }
        
        .nav-link:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        
        .nav-link.active {
            background-color: var(--bs-primary);
            color: white !important;
        }
        
        .card {
            border: 1px solid var(--st-border-color);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .btn {
            border-radius: 0.375rem;
        }
        
        .table {
            --bs-table-border-color: var(--st-border-color);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .priority-high { color: var(--st-danger); }
        .priority-urgent { color: var(--st-danger); font-weight: bold; }
        .priority-medium { color: var(--st-info); }
        .priority-low { color: var(--st-success); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
                        <i class="bi bi-tools me-2 fs-4 text-primary"></i>
                        <span class="navbar-brand mb-0">SupportTracker</span>
                    </div>
                    
                    <hr>
                    
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="/SupporTracker/dashboard" class="nav-link <?= $this->isActive('dashboard') ?>">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/tickets" class="nav-link <?= $this->isActive('tickets') ?>">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                Tickets
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/customers" class="nav-link <?= $this->isActive('customers') ?>">
                                <i class="bi bi-people me-2"></i>
                                Customers
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/assets" class="nav-link <?= $this->isActive('assets') ?>">
                                <i class="bi bi-laptop me-2"></i>
                                Assets
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/projects" class="nav-link <?= $this->isActive('projects') ?>">
                                <i class="bi bi-kanban me-2"></i>
                                Projects
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/invoices" class="nav-link <?= $this->isActive('invoices') ?>">
                                <i class="bi bi-receipt me-2"></i>
                                Invoices
                            </a>
                        </li>
                        <li>
                            <a href="/SupporTracker/reports" class="nav-link <?= $this->isActive('reports') ?>">
                                <i class="bi bi-graph-up me-2"></i>
                                Reports
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager'])): ?>
                        <li>
                            <a href="/SupporTracker/settings" class="nav-link <?= $this->isActive('settings') ?>">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                        <?php endif; ?>

                    </ul>
                    
                    <hr>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong><?= $_SESSION['user_name'] ?? 'Admin' ?></strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow">
                            <li><a class="dropdown-item" href="/SupporTracker/profile">Profile</a></li>
                            <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager'])): ?>
                            <li><a class="dropdown-item" href="/SupporTracker/settings">Settings</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/SupporTracker/logout">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $this->pageTitle ?? 'Dashboard' ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Global Search -->
                        <div class="position-relative me-3">
                            <input type="text" class="form-control form-control-sm" id="globalSearch" placeholder="Search customers, tickets, assets..." style="width: 300px;">
                            <div id="searchResults" class="position-absolute bg-body border rounded shadow-sm mt-1 w-100" style="display: none; z-index: 1050; max-height: 400px; overflow-y: auto;"></div>
                        </div>
                        
                        <?php 
                        // Debug: Show what's in session
                        // var_dump($_SESSION['user_locations']); 
                        if (isset($_SESSION['user_locations']) && count($_SESSION['user_locations']) > 0): ?>
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= $_SESSION['current_location_name'] ?? 'Select Location' ?>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach ($_SESSION['user_locations'] as $location): ?>
                                <li>
                                    <a class="dropdown-item" href="/SupporTracker/switch-location?id=<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['name']) ?>
                                        <?php if (isset($_SESSION['current_location_id']) && $_SESSION['current_location_id'] == $location['id']): ?>
                                            <i class="bi bi-check-lg ms-1 text-success"></i>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="theme-toggle">
                            <i class="bi bi-sun-fill" id="theme-icon"></i>
                        </button>
                        <?= $this->headerActions ?? '' ?>
                    </div>
                </div>
                
                <?= $this->content ?>
                <?php if (empty($this->content)): ?>
                    <div class="alert alert-warning">No content loaded</div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <!-- Modals Container -->
    <div id="modalContainer"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/SupporTracker/includes/password-toggle.js"></script>
    
    <script>
        // Theme switcher
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;
        
        // Get saved theme or default to auto
        const savedTheme = localStorage.getItem('theme') || 'auto';
        html.setAttribute('data-bs-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-moon-fill';
            } else {
                themeIcon.className = 'bi bi-sun-fill';
            }
        }
        
        // Auto-detect system theme if set to auto
        if (savedTheme === 'auto') {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            html.setAttribute('data-bs-theme', mediaQuery.matches ? 'dark' : 'light');
            
            mediaQuery.addEventListener('change', (e) => {
                if (localStorage.getItem('theme') === 'auto') {
                    html.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
                }
            });
        }
        
        // Global Search
        const searchInput = document.getElementById('globalSearch');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    fetch(`/SupporTracker/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data.results);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });
            
            searchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    searchResults.style.display = 'none';
                }, 200);
            });
            
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    searchResults.style.display = 'block';
                }
            });
        }
        
        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="p-3 text-muted">No results found</div>';
                searchResults.style.display = 'block';
                return;
            }
            
            let html = '';
            const groupedResults = {};
            
            // Group by type
            results.forEach(result => {
                if (!groupedResults[result.type]) {
                    groupedResults[result.type] = [];
                }
                groupedResults[result.type].push(result);
            });
            
            // Display grouped results
            Object.keys(groupedResults).forEach(type => {
                html += `<div class="px-3 py-1 bg-light border-bottom"><small class="text-muted text-uppercase fw-bold">${type}s</small></div>`;
                groupedResults[type].forEach(result => {
                    html += `
                        <a href="${result.url}" class="d-block px-3 py-2 text-decoration-none border-bottom" style="color: inherit;">
                            <div class="d-flex align-items-center">
                                <i class="bi ${result.icon} me-2 text-primary"></i>
                                <div>
                                    <div class="fw-medium">${result.title}</div>
                                    <small class="text-muted">${result.subtitle}</small>
                                </div>
                            </div>
                        </a>
                    `;
                });
            });
            
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }
    </script>
    
    <?php foreach ($this->scripts as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>