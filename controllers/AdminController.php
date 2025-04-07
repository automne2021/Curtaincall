<?php
class AdminController {
    private $conn;
    private $adminModel;
    private $playModel;
    private $theaterModel;
    private $bookingModel;
    private $userModel;
    
    public function __construct($conn) {
        require_once 'models/Admin.php';
        require_once 'models/Play.php';
        require_once 'models/Theater.php';
        require_once 'models/Booking.php';
        require_once 'models/User.php';
        
        $this->conn = $conn;
        $this->adminModel = new Admin($conn);
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
        $this->bookingModel = new Booking($conn);
        $this->userModel = new User($conn);
    }
    
    // Helper method to check if admin is logged in
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=admin/login');
            exit;
        }
    }
    
    // Login page
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['admin'])) {
            header('Location: index.php?route=admin/dashboard');
            exit;
        }
        
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validate login
            $admin = $this->adminModel->login($username, $password);
            
            if ($admin) {
                $_SESSION['admin'] = $admin;
                header('Location: index.php?route=admin/dashboard');
                exit;
            } else {
                $_SESSION['admin_login_error'] = 'Invalid username or password';
            }
        }
        
        // Display login form
        include 'views/admin/login.php';
    }
    
    // Logout
    public function logout() {
        unset($_SESSION['admin']);
        header('Location: index.php?route=admin/login');
        exit;
    }
    
    // Dashboard
    public function dashboard() {
        $this->checkAdminAuth();
        
        // Get dashboard statistics
        $stats = $this->adminModel->getDashboardStats();
        $recent_bookings = $this->adminModel->getRecentBookings(5);
        $popular_plays = $this->playModel->getPopularPlays(5);
        
        include 'views/admin/layouts/admin_header.php';
        include 'views/admin/dashboard.php';
        include 'views/admin/layouts/admin_footer.php';
    }
    
    // PLAYS MANAGEMENT
    
    // List all plays
    public function plays() {
        $this->checkAdminAuth();
        
        $plays = $this->playModel->getAllPlays();
        
        include 'views/admin/layouts/admin_header.php';
        include 'views/admin/plays/index.php';
        include 'views/admin/layouts/admin_footer.php';
    }
    
    // Create new play
    public function createPlay() {
        $this->checkAdminAuth();
        
        $theaters = $this->theaterModel->getAllTheaters();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'public/images/plays/';
                $temp_name = $_FILES['image']['tmp_name'];
                $name = basename($_FILES['image']['name']);
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_ext;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($temp_name, $target_file)) {
                    $image = $target_file;
                }
            }
            
            $play_data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'duration' => $_POST['duration'],
                'theater_id' => $_POST['theater_id'],
                'director' => $_POST['director'] ?? '',
                'cast' => $_POST['cast'] ?? '',
                'image' => $image
            ];
            
            $play_id = $this->playModel->createPlay($play_data);
            
            if ($play_id) {
                $_SESSION['success_message'] = 'Play created successfully';
                header('Location: index.php?route=admin/plays');
                exit;
            } else {
                $_SESSION['error_message'] = 'Error creating play';
            }
        }
        
        include 'views/admin/layouts/admin_header.php';
        include 'views/admin/plays/create.php';
        include 'views/admin/layouts/admin_footer.php';
    }
    
    // Edit play
    public function editPlay() {
        $this->checkAdminAuth();
        
        $play_id = $_GET['id'] ?? null;
        if (!$play_id) {
            $_SESSION['error_message'] = 'No play ID specified';
            header('Location: index.php?route=admin/plays');
            exit;
        }
        
        $play = $this->playModel->getPlayById($play_id);
        $theaters = $this->theaterModel->getAllTheaters();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle image upload
            $image = $play['image']; // Default to current image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'public/images/plays/';
                $temp_name = $_FILES['image']['tmp_name'];
                $name = basename($_FILES['image']['name']);
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_ext;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($temp_name, $target_file)) {
                    $image = $target_file;
                }
            }
            
            $play_data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'duration' => $_POST['duration'],
                'theater_id' => $_POST['theater_id'],
                'director' => $_POST['director'] ?? '',
                'cast' => $_POST['cast'] ?? '',
                'image' => $image
            ];
            
            $success = $this->playModel->updatePlay($play_id, $play_data);
            
            if ($success) {
                $_SESSION['success_message'] = 'Play updated successfully';
                header('Location: index.php?route=admin/plays');
                exit;
            } else {
                $_SESSION['error_message'] = 'Error updating play';
            }
        }
        
        include 'views/admin/layouts/admin_header.php';
        include 'views/admin/plays/edit.php';
        include 'views/admin/layouts/admin_footer.php';
    }
    
    // Delete play
    public function deletePlay() {
        $this->checkAdminAuth();
        
        $play_id = $_GET['id'] ?? null;
        if (!$play_id) {
            $_SESSION['error_message'] = 'No play ID specified';
            header('Location: index.php?route=admin/plays');
            exit;
        }
        
        // Check if play has bookings
        $has_bookings = $this->bookingModel->checkPlayHasBookings($play_id);
        
        if ($has_bookings) {
            $_SESSION['error_message'] = 'Cannot delete play with existing bookings';
            header('Location: index.php?route=admin/plays');
            exit;
        }
        
        $success = $this->playModel->deletePlay($play_id);
        
        if ($success) {
            $_SESSION['success_message'] = 'Play deleted successfully';
        } else {
            $_SESSION['error_message'] = 'Error deleting play';
        }
        
        header('Location: index.php?route=admin/plays');
        exit;
    }
    
    // THEATERS MANAGEMENT - Similar methods for other entities
    // Add similar methods for theaters, schedules, users, bookings
}