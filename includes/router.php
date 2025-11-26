<?php
class Router {
    private $routes = [];
    
    public function get($path, $controller) {
        $this->routes['GET'][$path] = $controller;
    }
    
    public function post($path, $controller) {
        $this->routes['POST'][$path] = $controller;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/SupporTracker', '', $path);
        
        file_put_contents('/tmp/router_debug.log', date('Y-m-d H:i:s') . " - Method: $method, Path: $path\n", FILE_APPEND);
        
        // Remove trailing slash
        $path = rtrim($path, '/');
        
        // Check authentication first
        if (!isset($_SESSION['admin_logged_in']) && $path !== '/logout' && $path !== '/simple_login') {
            // Redirect to simple login
            header('Location: /SupporTracker/simple_login');
            exit;
        }
        
        if ($path === '' || $path === '/') {
            $path = '/dashboard';
        }
        
        if (isset($this->routes[$method][$path])) {
            $controller = $this->routes[$method][$path];
            if (is_callable($controller)) {
                $controller();
            } else {
                global $pdo;
                try {
                    include_once $controller;
                    exit; // Prevent further execution
                } catch (Exception $e) {
                    error_log('Controller error: ' . $e->getMessage());
                    http_response_code(500);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                } catch (Error $e) {
                    error_log('Controller fatal error: ' . $e->getMessage());
                    http_response_code(500);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }
            }
        } else {
            http_response_code(404);
            echo "Page not found. Requested: $path";
        }
    }
}

$router = new Router();

// Define routes
$router->get('/dashboard', 'controllers/dashboard.php');
$router->get('/companies', 'controllers/companies.php');
$router->get('/company', 'controllers/company_detail.php');
$router->get('/assets', 'controllers/assets.php');
$router->get('/asset', 'controllers/asset_detail.php');
$router->get('/employees', 'controllers/employees.php');
$router->get('/employee', 'controllers/employee_detail.php');
$router->get('/workorders', 'controllers/workorders.php');
$router->get('/workorder', 'controllers/workorder_detail.php');
$router->get('/projects', 'controllers/projects.php');
$router->get('/parts', 'controllers/parts.php');
$router->get('/invoices', 'controllers/invoices.php');
$router->get('/invoice', 'controllers/invoice_detail.php');
$router->get('/settings', 'controllers/settings.php');
$router->get('/run_sql', 'run_sql.php');
$router->get('/simple_login', 'simple_login.php');
$router->post('/simple_login', 'simple_login.php');
$router->post('/set_location', 'controllers/set_location.php');
$router->get('/credentials', 'controllers/credentials.php');
$router->get('/search', 'controllers/search.php');
$router->get('/logout', 'logout.php');
$router->get('/phase2-test', 'phase2_test_checklist.php');
$router->get('/check-db', 'check_database.php');

$router->post('/companies', 'controllers/companies.php');
$router->post('/assets', 'controllers/assets.php');
$router->post('/employees', 'controllers/employees.php');
$router->post('/workorders', 'controllers/workorders.php');
$router->post('/workorder', 'controllers/workorder_detail.php');
$router->post('/projects', 'controllers/projects.php');
$router->post('/parts', 'controllers/parts.php');
$router->post('/invoices', 'controllers/invoices.php');
$router->post('/settings', 'controllers/settings.php');
$router->post('/credentials', 'controllers/credentials.php');
$router->post('/debug_post.php', 'debug_post.php');

// Dispatch the router
$router->dispatch();
?>