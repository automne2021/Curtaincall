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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $admin = $this->adminModel->getAdminByUsername($username);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Set admin session
                $_SESSION['admin'] = [
                    'admin_id' => $admin['admin_id'],
                    'username' => $admin['username']
                ];
                
                header('Location: index.php?route=admin/dashboard');
                exit;
            } else {
                $_SESSION['admin_login_error'] = 'Invalid username or password';
            }
        }
        
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
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/dashboard.php';
        include 'views/admin/layouts/footer.php';
    }
    
    // PLAYS MANAGEMENT
    
    // List all plays
    public function plays() {
        $this->checkAdminAuth();
        
        $plays = $this->playModel->getAllPlays();
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/plays/index.php';
        include 'views/admin/layouts/footer.php';
    }
    
    // Create new play
    public function createPlay() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = [];
            
            if (empty($_POST['title'])) {
                $errors['title'] = 'Title is required';
            }
            
            if (empty($_POST['theater_id'])) {
                $errors['theater_id'] = 'Theater is required';
            }
            
            if (empty($_POST['duration']) || !is_numeric($_POST['duration'])) {
                $errors['duration'] = 'Valid duration is required';
            }
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            
            // Handle image upload if present
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (!in_array(strtolower($ext), $allowed)) {
                    $errors['image'] = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF';
                } else {
                    $upload_dir = 'public/images/plays/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $new_filename = uniqid() . '.' . $ext;
                    $destination = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $image_path = $destination;
                    } else {
                        $errors['image'] = 'Failed to upload image';
                    }
                }
            }
            
            if (empty($errors)) {
                // Prepare play data
                $play_data = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'theater_id' => $_POST['theater_id'],
                    'duration' => $_POST['duration'],
                    'director' => $_POST['director'] ?? null,
                    'cast' => $_POST['cast'] ?? null,
                    'image' => $image_path
                ];
                
                $play_id = $this->playModel->createPlay($play_data);
                
                if ($play_id) {
                    $_SESSION['success_message'] = 'Play created successfully';
                    header('Location: index.php?route=admin/plays');
                    exit;
                } else {
                    $_SESSION['error_message'] = 'Error creating play';
                    
                    // If image was uploaded but DB insert failed, delete the uploaded image
                    if ($image_path && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            } else {
                $_SESSION['error_message'] = 'Please fix the errors in the form';
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                
                // If image was uploaded but validation failed, delete the uploaded image
                if (isset($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // Get theaters for dropdown
        $theaters = $this->theaterModel->getAllTheaters();
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/plays/createPlay.php';
        include 'views/admin/layouts/footer.php';
    }
    
    // Edit existing play
    public function editPlay() {
        $this->checkAdminAuth();
        
        $play_id = $_GET['id'] ?? null;
        if (!$play_id) {
            $_SESSION['error_message'] = 'No play ID specified';
            header('Location: index.php?route=admin/plays');
            exit;
        }
        
        $play = $this->playModel->getPlayById($play_id);
        if (!$play) {
            $_SESSION['error_message'] = 'Play not found';
            header('Location: index.php?route=admin/plays');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = [];
            
            if (empty($_POST['title'])) {
                $errors['title'] = 'Title is required';
            }
            
            if (empty($_POST['theater_id'])) {
                $errors['theater_id'] = 'Theater is required';
            }
            
            if (empty($_POST['duration']) || !is_numeric($_POST['duration'])) {
                $errors['duration'] = 'Valid duration is required';
            }
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            
            // Handle image upload if present
            $image_path = $play['image']; // Keep existing image by default
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (!in_array(strtolower($ext), $allowed)) {
                    $errors['image'] = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF';
                } else {
                    $upload_dir = 'public/images/plays/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $new_filename = uniqid() . '.' . $ext;
                    $destination = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                        $image_path = $destination;
                    } else {
                        $errors['image'] = 'Failed to upload image';
                    }
                }
            }
            
            if (empty($errors)) {
                // Prepare play data
                $play_data = [
                    'play_id' => $play_id,
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'theater_id' => $_POST['theater_id'],
                    'duration' => $_POST['duration'],
                    'director' => $_POST['director'] ?? null,
                    'cast' => $_POST['cast'] ?? null,
                    'image' => $image_path
                ];
                
                $old_image_path = $play['image'];
                $success = $this->playModel->updatePlay($play_data);
                
                if ($success) {
                    $_SESSION['success_message'] = 'Play updated successfully';
                    
                    // Delete old image if it was replaced and exists
                    if ($image_path != $old_image_path && $old_image_path && file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                    
                    header('Location: index.php?route=admin/plays');
                    exit;
                } else {
                    $_SESSION['error_message'] = 'Error updating play';
                    
                    // If a new image was uploaded but DB update failed, delete the new image
                    if ($image_path != $old_image_path && $image_path && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            } else {
                $_SESSION['error_message'] = 'Please fix the errors in the form';
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                
                // If a new image was uploaded but validation failed, delete the newly uploaded image
                if (isset($destination) && file_exists($destination) && $destination != $play['image']) {
                    unlink($destination);
                }
            }
        }
        
        // Get theaters for dropdown
        $theaters = $this->theaterModel->getAllTheaters();
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/plays/editPlay.php';
        include 'views/admin/layouts/footer.php';
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
        
        // Get the play to access its image path
        $play = $this->playModel->getPlayById($play_id);
        if (!$play) {
            $_SESSION['error_message'] = 'Play not found';
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
            // Delete image file if it exists
            if ($play['image'] && file_exists($play['image'])) {
                unlink($play['image']);
            }
            
            $_SESSION['success_message'] = 'Play deleted successfully';
        } else {
            $_SESSION['error_message'] = 'Error deleting play';
        }
        
        header('Location: index.php?route=admin/plays');
        exit;
    }
}