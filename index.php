<?php
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

// Check for remember token cookie and auto-login if applicable
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Load user model and check token
    require_once 'models/User.php';
    $userModel = new User($conn);
    $user = $userModel->getUserByRememberToken($token);
    
    if ($user) {
        // Auto login the user
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'fullname' => $user['name'] ?? $user['fullname'] ?? '',
            'avatar' => $user['avatar'] ?? null,
            'is_google_user' => !empty($user['google_id']),
            'role' => $user['role'] ?? 'user'
        ];
        
        // Refresh the token
        $new_token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        $userModel->storeRememberToken($user['user_id'], $new_token, $expires);
        setcookie('remember_token', $new_token, $expires, '/', '', isset($_SERVER['HTTPS']), true);
    }
}

// Check for admin remember token cookie and auto-login if applicable
if (!isset($_SESSION['admin']) && isset($_COOKIE['admin_remember_token'])) {
    $token = $_COOKIE['admin_remember_token'];
    
    // Load admin model and check token
    require_once 'models/Admin.php';
    $adminModel = new Admin($conn);
    $admin = $adminModel->getAdminByRememberToken($token);
    
    if ($admin) {
        // Auto login the admin
        $_SESSION['admin'] = [
            'admin_id' => $admin['admin_id'],
            'username' => $admin['username'],
            'email' => $admin['email']
        ];
        
        // Refresh the token
        $new_token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        $adminModel->storeRememberToken($admin['admin_id'], $new_token, $expires);
        setcookie('admin_remember_token', $new_token, $expires, '/', '', isset($_SERVER['HTTPS']), true);
    }
}

// Simple routing mechanism
$route = isset($_GET['route']) ? $_GET['route'] : 'home';
$routes['payment/process'] = ['controller' => 'PaymentController', 'action' => 'process'];
$routes['contact'] = ['controller' => 'ContactController', 'action' => 'index'];
$routes['contact/send'] = ['controller' => 'ContactController', 'action' => 'send'];
$routes['user/google-login'] = ['controller' => 'UserController', 'action' => 'googleLogin'];
$routes['user/google-callback'] = ['controller' => 'UserController', 'action' => 'googleCallback'];
$routes['user/disconnectGoogle'] = ['controller' => 'UserController', 'action' => 'disconnectGoogle'];
$routes['booking/cancelConfirmation'] = ['controller' => 'BookingController', 'action' => 'cancelConfirmation'];

// Search routes
$routes['search/index'] = ['controller' => 'SearchController', 'action' => 'index'];
$routes['search/ajaxSearch'] = ['controller' => 'SearchController', 'action' => 'ajaxSearch'];
$routes['search/ajaxSearchFull'] = ['controller' => 'SearchController', 'action' => 'ajaxSearchFull'];

// Admin routes
$routes['admin/login'] = ['controller' => 'AdminController', 'action' => 'login'];
$routes['admin/logout'] = ['controller' => 'AdminController', 'action' => 'logout'];
$routes['admin/dashboard'] = ['controller' => 'AdminController', 'action' => 'dashboard'];
// Plays management routes
$routes['admin/plays'] = ['controller' => 'AdminController', 'action' => 'plays'];
$routes['admin/createPlay'] = ['controller' => 'AdminController', 'action' => 'createPlay'];
$routes['admin/editPlay'] = ['controller' => 'AdminController', 'action' => 'editPlay'];
$routes['admin/deletePlay'] = ['controller' => 'AdminController', 'action' => 'deletePlay'];
$routes['admin/viewPlay'] = ['controller' => 'AdminController', 'action' => 'viewPlay'];
// Theater management routes
$routes['admin/theaters'] = ['controller' => 'TheaterController', 'action' => 'theaters'];
$routes['admin/createTheater'] = ['controller' => 'TheaterController', 'action' => 'createTheater'];
$routes['admin/editTheater'] = ['controller' => 'TheaterController', 'action' => 'editTheater'];
$routes['admin/deleteTheater'] = ['controller' => 'TheaterController', 'action' => 'deleteTheater'];
$routes['admin/viewTheater'] = ['controller' => 'TheaterController', 'action' => 'viewTheater'];
// Booking management routes
$routes['admin/bookings'] = ['controller' => 'BookingController', 'action' => 'bookings'];
$routes['admin/viewBooking'] = ['controller' => 'BookingController', 'action' => 'viewBooking'];
// User management routes
$routes['admin/users'] = ['controller' => 'UserController', 'action' => 'users'];
$routes['admin/viewUser'] = ['controller' => 'UserController', 'action' => 'viewUser'];
$routes['admin/deleteUser'] = ['controller' => 'UserController', 'action' => 'deleteUser'];
$routes['admin/userBookings'] = ['controller' => 'UserController', 'action' => 'userBookings'];

// Check if the current route exists in the predefined routes
if (isset($routes[$route])) {
    // Use the predefined controller and action
    $controller = $routes[$route]['controller'];
    $action = $routes[$route]['action'];
    $params = []; // No additional parameters for predefined routes
} else {
    // Parse the route (original logic)
    $parts = explode('/', trim($route, '/'));
    $controller = ucfirst(strtolower($parts[0] ?? 'home')) . 'Controller';
    $action = strtolower($parts[1] ?? 'index');
    $params = array_slice($parts, 2);

    // Use default controller if not specified
    if ($controller === 'Controller') {
        $controller = 'HomeController';
    }
}

// Include the requested controller
$controllerFile = 'controllers/' . $controller . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Create controller instance
    $controllerInstance = new $controller($conn);
    if (strpos($action, '-') !== false) {
        $camelCaseAction = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $action))));
        if (method_exists($controllerInstance, $camelCaseAction)) {
            $action = $camelCaseAction;
        }
    }
    // Check if the action exists
    if (method_exists($controllerInstance, $action)) {
        // Call the action with parameters
        call_user_func_array([$controllerInstance, $action], $params);
    } else {
        // Action not found - default to index
        echo "404 Not Found: Action '$action' not found in controller '$controller'";
        $controllerInstance->index();
    }
} else {
    // Controller not found - load 404 page
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Page Not Found</h1>";
}

// Close database connection
$conn->close();
