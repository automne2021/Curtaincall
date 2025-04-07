<?php
// filepath: c:\xampp\htdocs\Curtaincall\models\User.php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Register a new user
    public function register($username, $email, $password) {
        // Check if username or email already exists
        if ($this->userExists($username, $email)) {
            return false;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        return $stmt->execute();
    }

    // Check if user exists
    private function userExists($username, $email) {
        $sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    // Login a user
    public function login($login, $password) {
        // $login could be username or email
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("User not found: $login");
            return false; // User not found
        }
        
        $user = $result->fetch_assoc();
        
        // Log for debugging
        error_log("Stored hash: " . substr($user['password'], 0, 20) . "...");
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            error_log("Password verified for user: $login");
            return $user; // Password matches
        }
        
        error_log("Password verification failed for user: $login");
        return false; // Password doesn't match
    }

    // Update user profile
    public function updateProfile($user_id, $data) {
        $set_parts = [];
        $param_types = "";
        $param_values = [];
        
        // Build the dynamic SET part of the query
        foreach ($data as $key => $value) {
            if (in_array($key, ['fullname', 'phone', 'address', 'avatar'])) {
                $set_parts[] = "$key = ?";
                $param_types .= "s";
                $param_values[] = $value;
            }
        }
        
        // Add user_id to parameters
        $param_types .= "i";
        $param_values[] = $user_id;
        
        $sql = "UPDATE users SET " . implode(", ", $set_parts) . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        // Use reflection to pass parameters by reference
        $params = array_merge([$param_types], $param_values);
        $refs = [];
        foreach($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $refs);
        
        return $stmt->execute();
    }

    // Upload avatar
    public function updateAvatar($user_id, $avatar_path) {
        $sql = "UPDATE users SET avatar = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $avatar_path, $user_id);
        
        return $stmt->execute();
    }

    // Get user by ID
    public function getUserById($user_id) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }

    // Change password
    public function changePassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        return $stmt->execute();
    }
}