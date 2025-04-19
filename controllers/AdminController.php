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
            $remember = isset($_POST['remember']) ? true : false;
            
            $admin = $this->adminModel->getAdminByUsername($username);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Set admin session
                $_SESSION['admin'] = [
                    'admin_id' => $admin['admin_id'],
                    'username' => $admin['username'],
                    'email' => $admin['email']
                ];
                
                // If remember me is checked, set a persistent cookie
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store the token in the database
                    $this->adminModel->storeRememberToken($admin['admin_id'], $token, $expires);
                    
                    // Set the cookie
                    setcookie('admin_remember_token', $token, $expires, '/', '', isset($_SERVER['HTTPS']), true);
                }
                
                header('Location: index.php?route=admin/dashboard');
                exit;
            } else {
                $_SESSION['admin_login_error'] = 'Invalid username or password';
            }
        }
        
        include 'views/admin/login.php';
    }
    
    public function logout() {
        // Clear the admin_remember_token cookie if it exists
        if (isset($_COOKIE['admin_remember_token'])) {
            $token = $_COOKIE['admin_remember_token'];
            
            // Remove token from database
            if (isset($_SESSION['admin']['admin_id'])) {
                $this->adminModel->deleteRememberToken($_SESSION['admin']['admin_id'], $token);
            }
            
            // Clear cookie by expiring it
            setcookie('admin_remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
        }
        
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
                // Check if play_id follows the pattern (3 letters and 2 digits)
                if (!preg_match('/^[A-Z]{3}[0-9]{2}$/', $_POST['play_id'])) {
                    $errors['play_id'] = 'Play ID must follow the pattern: 3 uppercase letters followed by 2 digits (e.g., IDE05)';
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
            
            // Validate schedules
            if (!isset($_POST['schedules']) || !is_array($_POST['schedules']) || empty($_POST['schedules'])) {
                $errors['schedules'] = 'At least one schedule is required';
            } else {
                foreach ($_POST['schedules'] as $index => $schedule) {
                    if (empty($schedule['date'])) {
                        $errors["date_$index"] = 'Date is required';
                    }
                    if (empty($schedule['start_time'])) {
                        $errors["start_time_$index"] = 'Start time is required';
                    }
                    if (empty($schedule['end_time'])) {
                        $errors["end_time_$index"] = 'End time is required';
                    }
                }
            }
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            $_POST['description'] = $this->sanitizeHtml($_POST['description']);
            
            // Handle image upload if present
            $image_path = 'public/images/plays/default.jpg'; // Default image
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
                    // First, create the play
                    $play_data = [
                        'play_id' => $_POST['play_id'],
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'theater_id' => $_POST['theater_id'],
                        'image' => $image_path
                    ];
                    
                    $success = $this->playModel->createPlay($play_data);
                    
                    if ($success) {
                        // Then, create schedules for the play
                        $scheduleModel = new Schedule($this->conn);
                        $scheduleSuccess = $scheduleModel->updateOrCreateSchedules($_POST['play_id'], $_POST['schedules']);
                        
                        if (!$scheduleSuccess) {
                            throw new Exception('Failed to create schedules');
                        }
                        
                        // Create seats based on theater's seat map
                        $seatModel = new Seat($this->conn);
                        $seat_map = $seatModel->getSeatMapByTheater($_POST['theater_id']);
                        
                        foreach ($seat_map as $seat) {
                            $seat_data = [
                                'theater_id' => $_POST['theater_id'],
                                'play_id' => $_POST['play_id'],
                                'seat_id' => $seat['seat_id'],
                                'status' => 'Available'
                            ];
                            
                            $seatModel->createSeat($seat_data);
                        }
                        
                        // Commit transaction
                        $this->conn->commit();
                        
                        $_SESSION['success_message'] = 'Play created successfully';
                        header('Location: index.php?route=admin/plays');
                        exit;
                    } else {
                        throw new Exception('Failed to create play');
                    }
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $this->conn->rollback();
                    
                    $_SESSION['error_message'] = 'Error creating play: ' . $e->getMessage();
                    
                    // If an image was uploaded but DB insert failed, delete the image
                    if ($image_path != 'public/images/plays/default.jpg' && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            } else {
                $_SESSION['error_message'] = 'Please fix the errors in the form';
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
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
            
            if (empty($_POST['description'])) {
                $errors['description'] = 'Description is required';
            }
            $_POST['description'] = $this->sanitizeHtml($_POST['description']);

            // Validate schedules
            if (empty($_POST['schedules']) || !is_array($_POST['schedules'])) {
                $errors['schedules'] = 'At least one schedule is required';
            } else {
                foreach ($_POST['schedules'] as $index => $schedule) {
                    if (empty($schedule['date'])) {
                        $errors["date_$index"] = 'Date is required';
                    }
                    if (empty($schedule['start_time'])) {
                        $errors["start_time_$index"] = 'Start time is required';
                    }
                    if (empty($schedule['end_time'])) {
                        $errors["end_time_$index"] = 'End time is required';
                    }
                }
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
                // Begin transaction
                $this->conn->begin_transaction();
                
                try {
                    // Prepare play data
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
                        // Handle schedules
                        if (isset($_POST['schedules']) && is_array($_POST['schedules'])) {
                            $scheduleModel = new Schedule($this->conn);
                            $scheduleSuccess = $scheduleModel->updateOrCreateSchedules($play_id, $_POST['schedules']);
                            
                            if (!$scheduleSuccess) {
                                throw new Exception('Failed to update schedules');
                            }
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
        
        // Get all schedules for this play
        $scheduleModel = new Schedule($this->conn);
        $schedules = $scheduleModel->getSchedulesByPlayId($play_id);
        
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

    public function viewPlay() {
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
        
        // Get all schedules for this play
        $schedules = $this->scheduleModel->getSchedulesByPlayId($play_id);
        
        // Get theater information
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/plays/viewPlay.php';
        include 'views/admin/layouts/footer.php';
    }

    private function sanitizeHtml($html) {
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