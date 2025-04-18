<?php
require_once 'models/User.php';

class UserController
{
    private $conn;
    private $userModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    // Helper method to sanitize input
    private function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function register()
    {
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

    public function login()
    {
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

    public function logout()
    {
        // Destroy the user session
        unset($_SESSION['user']);

        // Set logged-out message
        $_SESSION['success_message'] = 'You have been logged out successfully.';

        // Redirect to home page
        header('Location: index.php');
        exit;
    }

    public function profile()
    {
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

    public function updateProfile()
    {
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
    private function uploadAvatar($file)
    {
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

    public function changePassword()
    {
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

    public function googleLogin()
    {
        try {
            // Add these lines to properly initialize the Google client
            require_once __DIR__ . '/../config/google_auth.php';
            require_once __DIR__ . '/../vendor/autoload.php';

            // Create Google client
            $client = new Google\Client();
            $client->setClientId(GOOGLE_CLIENT_ID);
            $client->setClientSecret(GOOGLE_CLIENT_SECRET);
            $client->setRedirectUri(GOOGLE_REDIRECT_URI);
            $client->addScope("email");
            $client->addScope("profile");

            // Generate the URL to which we will redirect the user
            $authUrl = $client->createAuthUrl();
            //echo "Redirect URI: " . GOOGLE_REDIRECT_URI;
            //exit;
            // Your existing code
            header('Location: ' . $authUrl);
            exit;
        } catch (Exception $e) {
            // Your existing error handling
            error_log('Google login error: ' . $e->getMessage());
            $_SESSION['login_errors']['general'] = 'Có lỗi xảy ra khi kết nối với Google. Vui lòng thử lại sau.';
            header('Location: index.php?route=user/login');
            exit;
        }
    }

    public function googleCallback()
    {
        require_once __DIR__ . '/../config/google_auth.php';
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new Google\Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);

        try {
            // Exchange the authorization code for an access token
            if (isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $client->setAccessToken($token);

                // Get user profile
                $google_oauth = new Google\Service\Oauth2($client);
                $google_account_info = $google_oauth->userinfo->get();

                // Extract user details
                $email = $google_account_info->email;
                $name = $google_account_info->name;
                $google_id = $google_account_info->id;
                $picture = $google_account_info->picture;

                // Check if user exists in database by google_id
                $user = $this->userModel->getUserByGoogleId($google_id);

                if (!$user) {
                    // Check if user with this email already exists
                    $user_by_email = $this->userModel->getUserByEmail($email);

                    if ($user_by_email) {
                        // Update existing user with Google ID
                        $this->userModel->updateGoogleId($user_by_email['user_id'], $google_id);
                        $user = $user_by_email;
                    } else {
                        // Create new user
                        $username = $this->generateUsername($name);
                        $random_password = bin2hex(random_bytes(8)); // Generate random password
                        $user_id = $this->userModel->registerUser([
                            'username' => $username,
                            'email' => $email,
                            'password' => password_hash($random_password, PASSWORD_DEFAULT),
                            'name' => $name,
                            'google_id' => $google_id,
                            'avatar' => $picture
                        ]);

                        $user = $this->userModel->getUserById($user_id);
                    }
                }

                // Log in the user
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'avatar' => $user['avatar'] ?? null,
                    'is_google_user' => true
                ];

                // Redirect to previous page or home
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);

                header('Location: ' . $redirect);
                exit;
            } else {
                // Error occurred
                $_SESSION['login_errors']['general'] = 'Không thể đăng nhập bằng Google. Vui lòng thử lại sau.';
                header('Location: index.php?route=user/login');
                exit;
            }
        } catch (Exception $e) {
            // Log the error and show a user-friendly message
            error_log('Google login error: ' . $e->getMessage());
            $_SESSION['login_errors']['general'] = 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại sau.';
            header('Location: index.php?route=user/login');
            exit;
        }
    }

    private function generateUsername($name)
    {
        // Remove the transliterator_transliterate function and use a simpler approach
        // Convert accented characters to ASCII
        $name = $this->removeAccents($name);

        // Replace spaces and special characters with empty string
        $base_username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

        // Ensure username is not empty (fallback)
        if (empty($base_username)) {
            $base_username = 'user';
        }

        $username = $base_username;
        $i = 1;

        // Check if username exists, if so, add a number
        while ($this->userModel->getUserByUsername($username)) {
            $username = $base_username . $i;
            $i++;
        }

        return $username;
    }

    // Helper function to remove accents
    private function removeAccents($string)
    {
        if (function_exists('iconv')) {
            // Try using iconv if available
            $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        }

        // Additional replacements for common accented characters
        $unwanted_array = [
            'á' => 'a',
            'à' => 'a',
            'ả' => 'a',
            'ã' => 'a',
            'ạ' => 'a',
            'ă' => 'a',
            'ắ' => 'a',
            'ằ' => 'a',
            'ẳ' => 'a',
            'ẵ' => 'a',
            'ặ' => 'a',
            'â' => 'a',
            'ấ' => 'a',
            'ầ' => 'a',
            'ẩ' => 'a',
            'ẫ' => 'a',
            'ậ' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ẻ' => 'e',
            'ẽ' => 'e',
            'ẹ' => 'e',
            'ê' => 'e',
            'ế' => 'e',
            'ề' => 'e',
            'ể' => 'e',
            'ễ' => 'e',
            'ệ' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'ỉ' => 'i',
            'ĩ' => 'i',
            'ị' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'ỏ' => 'o',
            'õ' => 'o',
            'ọ' => 'o',
            'ô' => 'o',
            'ố' => 'o',
            'ồ' => 'o',
            'ổ' => 'o',
            'ỗ' => 'o',
            'ộ' => 'o',
            'ơ' => 'o',
            'ớ' => 'o',
            'ờ' => 'o',
            'ở' => 'o',
            'ỡ' => 'o',
            'ợ' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'ủ' => 'u',
            'ũ' => 'u',
            'ụ' => 'u',
            'ư' => 'u',
            'ứ' => 'u',
            'ừ' => 'u',
            'ử' => 'u',
            'ữ' => 'u',
            'ự' => 'u',
            'ý' => 'y',
            'ỳ' => 'y',
            'ỷ' => 'y',
            'ỹ' => 'y',
            'ỵ' => 'y',
            'đ' => 'd',
            // Upper case
            'Á' => 'A',
            'À' => 'A',
            'Ả' => 'A',
            'Ã' => 'A',
            'Ạ' => 'A',
            'Ă' => 'A',
            'Ắ' => 'A',
            'Ằ' => 'A',
            'Ẳ' => 'A',
            'Ẵ' => 'A',
            'Ặ' => 'A',
            'Â' => 'A',
            'Ấ' => 'A',
            'Ầ' => 'A',
            'Ẩ' => 'A',
            'Ẫ' => 'A',
            'Ậ' => 'A',
            'É' => 'E',
            'È' => 'E',
            'Ẻ' => 'E',
            'Ẽ' => 'E',
            'Ẹ' => 'E',
            'Ê' => 'E',
            'Ế' => 'E',
            'Ề' => 'E',
            'Ể' => 'E',
            'Ễ' => 'E',
            'Ệ' => 'E',
            'Í' => 'I',
            'Ì' => 'I',
            'Ỉ' => 'I',
            'Ĩ' => 'I',
            'Ị' => 'I',
            'Ó' => 'O',
            'Ò' => 'O',
            'Ỏ' => 'O',
            'Õ' => 'O',
            'Ọ' => 'O',
            'Ô' => 'O',
            'Ố' => 'O',
            'Ồ' => 'O',
            'Ổ' => 'O',
            'Ỗ' => 'O',
            'Ộ' => 'O',
            'Ơ' => 'O',
            'Ớ' => 'O',
            'Ờ' => 'O',
            'Ở' => 'O',
            'Ỡ' => 'O',
            'Ợ' => 'O',
            'Ú' => 'U',
            'Ù' => 'U',
            'Ủ' => 'U',
            'Ũ' => 'U',
            'Ụ' => 'U',
            'Ư' => 'U',
            'Ứ' => 'U',
            'Ừ' => 'U',
            'Ử' => 'U',
            'Ữ' => 'U',
            'Ự' => 'U',
            'Ý' => 'Y',
            'Ỳ' => 'Y',
            'Ỷ' => 'Y',
            'Ỹ' => 'Y',
            'Ỵ' => 'Y',
            'Đ' => 'D',
        ];

        return strtr($string, $unwanted_array);
    }

    public function disconnectGoogle()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=user/login');
            exit;
        }

        $user_id = $_SESSION['user']['user_id'];

        // Remove Google ID from user account
        $success = $this->userModel->updateGoogleId($user_id, null);

        if ($success) {
            // Update session
            if (isset($_SESSION['user']['is_google_user'])) {
                unset($_SESSION['user']['is_google_user']);
            }

            $_SESSION['success_message'] = 'Đã hủy kết nối tài khoản Google thành công.';
        } else {
            $_SESSION['error_message'] = 'Không thể hủy kết nối tài khoản Google. Vui lòng thử lại sau.';
        }

        header('Location: index.php?route=user/profile');
        exit;
    }
}
