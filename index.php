<?php
// filepath: c:\Users\VY\Downloads\curtaincall\index.php
session_start();

// Define application path constants
define('BASE_PATH', __DIR__);
define('PUBLIC_PATH', BASE_PATH . '/public');

// Define base URL for the application
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script = dirname($_SERVER['SCRIPT_NAME']);
$base = rtrim($script, '/\\');
define('BASE_URL', $protocol . $host . $base . '/');

// Include helper functions
require_once 'helpers/url_helpers.php';
require_once 'helpers/sort_helpers.php';

// Load the database configuration
require_once 'config/database.php';
require_once 'config/config.php';

// Simple routing mechanism
$route = isset($_GET['route']) ? $_GET['route'] : 'home';
$routes['payment/process'] = ['controller' => 'PaymentController', 'action' => 'process'];
$routes['contact'] = ['controller' => 'ContactController', 'action' => 'index'];
$routes['contact/send'] = ['controller' => 'ContactController', 'action' => 'send'];


// Parse the route
$parts = explode('/', trim($route, '/'));
$controller = ucfirst(strtolower($parts[0] ?? 'home')) . 'Controller';
$action = strtolower($parts[1] ?? 'index');
$params = array_slice($parts, 2);

// Use default controller if not specified
if ($controller === 'Controller') {
    $controller = 'HomeController';
}

// Include the requested controller
$controllerFile = 'controllers/' . $controller . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Create controller instance
    $controllerInstance = new $controller($conn);

    // Check if the action exists
    if (method_exists($controllerInstance, $action)) {
        // Call the action with parameters
        call_user_func_array([$controllerInstance, $action], $params);
    } else {
        // Action not found - default to index
        $controllerInstance->index();
    }
} else {
    // Controller not found - load 404 page
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Page Not Found</h1>";
}

// Close database connection
$conn->close();
