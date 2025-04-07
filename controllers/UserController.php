<?php
// filepath: c:\xampp\htdocs\Curtaincall\controllers\UserController.php
require_once 'models/User.php';

class UserController {
    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    // Helper method to sanitize input
    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Server-side validation
            $errors = [];
            
            $username = $this->sanitizeInput($_POST['username'] ?? '');
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Debug info (remove in production)
            error_log("Password: " . substr($password, 0, 1) . "..." . substr($password, -1) . " (length: " . strlen($password) . ")");
            error_log("Confirm: " . substr($confirm_password, 0, 1) . "..." . substr($confirm_password, -1) . " (length: " . strlen($confirm_password) . ")");
            error_log("Equal: " . ($password === $confirm_password ? "true" : "false"));
            
            // Validate username
            if (empty($username) || strlen($username) < 4) {
                $errors['username'] = 'Username must be at least 4 characters';
            }
            
            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address';
            }
            
            // Validate password
            if (empty($password) || strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            
            // Validate password confirmation - FIXED to match exactly what's posted
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                // Attempt to register user
                if ($this->userModel->register($username, $email, $password)) {
                    // Registration successful - auto login
                    $user = $this->userModel->login($email, $password);
                    $_SESSION['user'] = $user;
                    
                    // Set success message
                    $_SESSION['success_message'] = 'Registration successful! Welcome to CurtainCall.';
                    
                    // Redirect to home page
                    header('Location: index.php');
                    exit;
                } else {
                    $errors['general'] = 'Username or email already exists';
                }
            }
            
            // If we got here, there are errors to display
            $_SESSION['register_errors'] = $errors;
            $_SESSION['form_data'] = ['username' => $username, 'email' => $email];
            
            // Redirect back to register form
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // If GET request, show the register page
        include 'views/layouts/header.php';
        include 'views/auth/register.php';
        include 'views/layouts/footer.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Server-side validation
            $errors = [];
            
            $login = $this->sanitizeInput($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Basic validation
            if (empty($login)) {
                $errors['login'] = 'Please enter your username or email';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Please enter your password';
            }
            
            if (empty($errors)) {
                // Attempt to login
                $user = $this->userModel->login($login, $password);
                
                if ($user) {
                    // Login successful
                    $_SESSION['user'] = $user;
                    
                    // Set success message
                    $_SESSION['success_message'] = 'Login successful!';
                    
                    // Get redirect URL
                    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                    unset($_SESSION['redirect_after_login']);
                    
                    // Check if this is an AJAX request
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        // Return JSON response for AJAX
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'redirect' => $redirect
                        ]);
                        exit;
                    }
                    
                    // Regular form submission - redirect
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $errors['general'] = 'Invalid username or password';
                    error_log("Login failed for user: $login - Invalid credentials");
                }
            }
            
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Return JSON response for AJAX with errors
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => $errors
                ]);
                exit;
            }
            
            // Regular form submission - store errors in session and redirect
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_data'] = ['login' => $login];
            
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // If GET request, show the login page
        include 'views/layouts/header.php';
        include 'views/auth/login.php';
        include 'views/layouts/footer.php';
    }

    public function logout() {
        // Destroy the user session
        unset($_SESSION['user']);
        
        // Set logged-out message
        $_SESSION['success_message'] = 'You have been logged out successfully.';
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    }

    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = 'index.php?route=user/profile';
            $_SESSION['error_message'] = 'Please log in to view your profile.';
            header('Location: index.php?route=user/login');
            exit;
        }

        $user_id = $_SESSION['user']['user_id'];
        $user = $this->userModel->getUserById($user_id);
        
        // Update the session with fresh user data
        $_SESSION['user'] = $user;
        
        include 'views/layouts/header.php';
        include 'views/auth/profile.php';
        include 'views/layouts/footer.php';
    }

    public function updateProfile() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to update your profile.';
            header('Location: index.php?route=user/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user']['user_id'];
            
            // Prepare data for update
            $data = [
                'fullname' => $this->sanitizeInput($_POST['fullname'] ?? ''),
                'phone' => $this->sanitizeInput($_POST['phone'] ?? ''),
                'address' => $this->sanitizeInput($_POST['address'] ?? '')
            ];
            
            // Handle avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                $avatar_path = $this->uploadAvatar($_FILES['avatar']);
                if ($avatar_path) {
                    $data['avatar'] = $avatar_path;
                }
            }
            
            // Update profile
            if ($this->userModel->updateProfile($user_id, $data)) {
                $_SESSION['success_message'] = 'Profile updated successfully';
                
                // Refresh user data in session
                $user = $this->userModel->getUserById($user_id);
                $_SESSION['user'] = $user;
            } else {
                $_SESSION['error_message'] = 'Failed to update profile';
            }
            
            header('Location: index.php?route=user/profile');
            exit;
        }
    }

    // Helper method to upload an avatar
    private function uploadAvatar($file) {
        $target_dir = "public/images/avatars/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid('user_') . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            $_SESSION['error_message'] = "File is not an image.";
            return false;
        }
        
        // Check file size (limit to 2MB)
        if ($file['size'] > 2000000) {
            $_SESSION['error_message'] = "Sorry, your file is too large (max 2MB).";
            return false;
        }
        
        // Allow only certain file formats
        if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
            $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            return false;
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file;
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
            return false;
        }
    }

    public function changePassword() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to change your password.';
            header('Location: index.php?route=user/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user']['user_id'];
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $errors = [];
            
            // Verify current password
            if (!$this->userModel->login($_SESSION['user']['username'], $current_password)) {
                $errors['current_password'] = 'Current password is incorrect';
            }
            
            // Validate new password
            if (empty($new_password) || strlen($new_password) < 6) {
                $errors['new_password'] = 'New password must be at least 6 characters';
            }
            
            // Confirm passwords match
            if ($new_password !== $confirm_password) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                if ($this->userModel->changePassword($user_id, $new_password)) {
                    $_SESSION['success_message'] = 'Password changed successfully';
                    header('Location: index.php?route=user/profile');
                    exit;
                } else {
                    $errors['general'] = 'Failed to change password';
                }
            }
            
            $_SESSION['password_errors'] = $errors;
            header('Location: index.php?route=user/profile');
            exit;
        }
    }
}