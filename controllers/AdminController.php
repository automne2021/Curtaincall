<?php
class AdminController {
    private $conn;
    private $adminModel;
    private $playModel;
    private $theaterModel;
    private $bookingModel;
    private $userModel;
    private $scheduleModel;
    private $seatModel;
    
    public function __construct($conn) {
        require_once 'models/Admin.php';
        require_once 'models/Play.php';
        require_once 'models/Theater.php';
        require_once 'models/Booking.php';
        require_once 'models/User.php';
        require_once 'models/Schedule.php';
        require_once 'models/Seat.php';
        
        $this->conn = $conn;
        $this->adminModel = new Admin($conn);
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
        $this->bookingModel = new Booking($conn);
        $this->userModel = new User($conn);
        $this->scheduleModel = new Schedule($conn);
        $this->seatModel = new Seat($conn);
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
        
        // Get graph data
        $revenue_chart_data = $this->adminModel->getMonthlyRevenueData();
        $bookings_chart_data = $this->adminModel->getBookingsByStatusData();
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/dashboard.php';
        include 'views/admin/layouts/footer.php';
    }
    
    // PLAYS MANAGEMENT
    
    // List all plays
    public function plays() {
        $this->checkAdminAuth();
        
        // Get current page from query string, default to 1 if not set
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 10;
        
        // Get paginated plays
        $result = $this->playModel->getAllPlays($page, $per_page);
        $plays = $result['plays'];
        $pagination = $result['pagination'];
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/plays/plays.php';
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
            
            if (empty($_POST['play_id'])) {
                $errors['play_id'] = 'Play ID is required';
            } else {
                // Check if play_id follows the pattern (3 letters followed by 2 digits)
                if (!preg_match('/^[A-Z]{3}\d{2}$/', $_POST['play_id'])) {
                    $errors['play_id'] = 'Play ID must follow the pattern: 3 uppercase letters followed by 2 digits (e.g., IDE09)';
                }
                
                // Check if play_id already exists
                $existingPlay = $this->playModel->getPlayById($_POST['play_id']);
                if ($existingPlay) {
                    $errors['play_id'] = 'This Play ID already exists. Please use another one.';
                }
            }
            
            if (empty($_POST['theater_id'])) {
                $errors['theater_id'] = 'Theater is required';
            }
            
            if (empty($_POST['date'])) {
                $errors['date'] = 'Date is required';
            }
            
            if (empty($_POST['start_time'])) {
                $errors['start_time'] = 'Start time is required';
            }
            
            if (empty($_POST['end_time'])) {
                $errors['end_time'] = 'End time is required';
            }
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            $_POST['description'] = $this->sanitizeHtml($_POST['description']);
            
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
                // Begin transaction
                $this->conn->begin_transaction();
                
                try {
                    // Prepare play data - removed director and cast fields
                    $play_data = [
                        'play_id' => $_POST['play_id'],
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'theater_id' => $_POST['theater_id'],
                        'image' => $image_path,
                        'views' => 0
                    ];
                    
                    // Create the play
                    $play_created = $this->playModel->createPlay($play_data);
                    
                    if ($play_created) {
                        // Add schedule
                        $schedule_data = [
                            'play_id' => $_POST['play_id'],
                            'date' => $_POST['date'],
                            'start_time' => $_POST['start_time'],
                            'end_time' => $_POST['end_time']
                        ];
                        
                        $schedule_created = $this->scheduleModel->createSchedule($schedule_data);
                        
                        if ($schedule_created) {
                            // Create seats based on theater's seat map
                            $seat_map = $this->seatModel->getSeatMapByTheater($_POST['theater_id']);
                            
                            foreach ($seat_map as $seat) {
                                $seat_data = [
                                    'theater_id' => $_POST['theater_id'],
                                    'play_id' => $_POST['play_id'],
                                    'seat_id' => $seat['seat_id'],
                                    'status' => 'Available'
                                ];
                                
                                $this->seatModel->createSeat($seat_data);
                            }
                            
                            // Commit transaction
                            $this->conn->commit();
                            
                            $_SESSION['success_message'] = 'Play created successfully';
                            header('Location: index.php?route=admin/plays');
                            exit;
                        } else {
                            throw new Exception('Failed to create schedule');
                        }
                    } else {
                        throw new Exception('Failed to create play');
                    }
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $this->conn->rollback();
                    
                    $_SESSION['error_message'] = 'Error creating play: ' . $e->getMessage();
                    
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
        
        // Get the schedule for this play
        $schedule = $this->scheduleModel->getScheduleByPlayId($play_id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = [];
            
            if (empty($_POST['title'])) {
                $errors['title'] = 'Title is required';
            }
            
            if (empty($_POST['theater_id'])) {
                $errors['theater_id'] = 'Theater is required';
            }
            
            if (empty($_POST['date'])) {
                $errors['date'] = 'Date is required';
            }
            
            if (empty($_POST['start_time'])) {
                $errors['start_time'] = 'Start time is required';
            }
            
            if (empty($_POST['end_time'])) {
                $errors['end_time'] = 'End time is required';
            }
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            $_POST['description'] = $this->sanitizeHtml($_POST['description']);

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
                // Begin transaction
                $this->conn->begin_transaction();
                
                try {
                    // Prepare play data - removed director and cast fields
                    $play_data = [
                        'play_id' => $play_id,
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'theater_id' => $_POST['theater_id'],
                        'image' => $image_path
                    ];
                    
                    $old_image_path = $play['image'];
                    $theater_changed = $play['theater_id'] != $_POST['theater_id'];
                    $success = $this->playModel->updatePlay($play_data);
                    
                    if ($success) {
                        // Update schedule
                        $schedule_data = [
                            'play_id' => $play_id,
                            'date' => $_POST['date'],
                            'start_time' => $_POST['start_time'],
                            'end_time' => $_POST['end_time']
                        ];
                        
                        if ($schedule) {
                            // Update existing schedule
                            $this->scheduleModel->updateSchedule($schedule_data);
                        } else {
                            // Create new schedule if none exists
                            $this->scheduleModel->createSchedule($schedule_data);
                        }
                        
                        // If theater changed, update seats
                        if ($theater_changed) {
                            // Delete old seats
                            $this->seatModel->deleteSeats($play_id);
                            
                            // Create new seats based on new theater's seat map
                            $seat_map = $this->seatModel->getSeatMapByTheater($_POST['theater_id']);
                            
                            foreach ($seat_map as $seat) {
                                $seat_data = [
                                    'theater_id' => $_POST['theater_id'],
                                    'play_id' => $play_id,
                                    'seat_id' => $seat['seat_id'],
                                    'status' => 'Available'
                                ];
                                
                                $this->seatModel->createSeat($seat_data);
                            }
                        }
                        
                        // Commit transaction
                        $this->conn->commit();
                        
                        $_SESSION['success_message'] = 'Play updated successfully';
                        
                        // Delete old image if it was replaced and exists
                        if ($image_path != $old_image_path && $old_image_path && file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                        
                        header('Location: index.php?route=admin/plays');
                        exit;
                    } else {
                        throw new Exception('Failed to update play');
                    }
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $this->conn->rollback();
                    
                    $_SESSION['error_message'] = 'Error updating play: ' . $e->getMessage();
                    
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

    private function sanitizeHtml($html) {
        // Allow a more comprehensive set of HTML tags that are needed for formatting
        $allowedTags = '<p><br><h1><h2><h3><h4><h5><h6><strong><b><em><i><u><ul><ol><li><blockquote><table><thead><tbody><tr><td><th><hr><span><div><img><a><code><pre><sup><sub><strike><del><ins>';
        
        // First, strip all tags except allowed ones
        $html = strip_tags($html, $allowedTags);
        
        // Remove any potential JavaScript event handlers
        $html = preg_replace('/on\w+="[^"]*"/', '', $html);
        $html = preg_replace('/on\w+=\'[^\']*\'/', '', $html);
        
        // Remove any src="javascript:" or href="javascript:"
        $html = preg_replace('/(src|href)\s*=\s*"javascript:[^"]*"/i', '', $html);
        $html = preg_replace('/(src|href)\s*=\s*\'javascript:[^\']*\'/i', '', $html);
        
        return $html;
    }
}