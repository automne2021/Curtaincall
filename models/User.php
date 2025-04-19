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

    public function getUserByGoogleId($google_id) {
        $sql = "SELECT * FROM users WHERE google_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function updateGoogleId($user_id, $google_id) {
        $sql = "UPDATE users SET google_id = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $google_id, $user_id);
        $stmt->execute();
        
        return $stmt->affected_rows > 0;
    }
    
    public function registerUser($data) {
        // Check for Google ID field
        $has_google_id = isset($data['google_id']) && !empty($data['google_id']);
        
        if ($has_google_id) {
            $sql = "INSERT INTO users (username, email, password, name, google_id, avatar) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssss", 
                $data['username'], 
                $data['email'], 
                $data['password'], 
                $data['name'], 
                $data['google_id'],
                $data['avatar']
            );
        } else {
            // Your existing register code
            $sql = "INSERT INTO users (username, email, password, name) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssss", 
                $data['username'], 
                $data['email'], 
                $data['password'], 
                $data['name']
            );
        }
        
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            return $stmt->insert_id;
        }
        
        return false;
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }

    public function getPaginatedUsers($page = 1, $per_page = 10) {
        try {
            // Calculate offset for pagination
            $offset = ($page - 1) * $per_page;
            
            // Count total users
            $countStmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users");
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            
            // Get users with pagination
            $stmt = $this->conn->prepare("
                SELECT * FROM users
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            
            // Return both users and pagination data
            return [
                'users' => $users,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $per_page,
                    'current_page' => $page,
                    'last_page' => ceil($total / $per_page)
                ]
            ];
        } catch (Exception $e) {
            error_log("Error in getPaginatedUsers: " . $e->getMessage());
            return [
                'users' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $per_page,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ];
        }
    }
    
    public function deleteUser($user_id) {
        try {
            // Start transaction
            $this->conn->begin_transaction();
            
            // Delete user's bookings first
            $bookingsStmt = $this->conn->prepare("DELETE FROM bookings WHERE user_id = ?");
            $bookingsStmt->bind_param("i", $user_id);
            $bookingsStmt->execute();
            
            // Then delete the user
            $userStmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
            $userStmt->bind_param("i", $user_id);
            $userStmt->execute();
            $result = $userStmt->affected_rows > 0;
            
            // Commit transaction
            $this->conn->commit();
            
            return $result;
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollback();
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function storeRememberToken($user_id, $token, $expires) {
        try {
            // First, delete any existing tokens for this user
            $deleteStmt = $this->conn->prepare("DELETE FROM user_tokens WHERE user_id = ?");
            $deleteStmt->bind_param("i", $user_id);
            $deleteStmt->execute();
            
            // Now insert the new token
            $expires_at = date('Y-m-d H:i:s', $expires);
            $stmt = $this->conn->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $token, $expires_at);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error storing remember token: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getUserByRememberToken($token) {
        try {
            $current_time = date('Y-m-d H:i:s');
            $stmt = $this->conn->prepare("
                SELECT u.* 
                FROM users u
                JOIN user_tokens t ON u.user_id = t.user_id
                WHERE t.token = ? AND t.expires_at > ?
            ");
            $stmt->bind_param("ss", $token, $current_time);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log('Error getting user by remember token: ' . $e->getMessage());
            return null;
        }
    }
    
    public function deleteRememberToken($user_id, $token = null) {
        try {
            if ($token) {
                // Delete specific token
                $stmt = $this->conn->prepare("DELETE FROM user_tokens WHERE user_id = ? AND token = ?");
                $stmt->bind_param("is", $user_id, $token);
            } else {
                // Delete all tokens for user
                $stmt = $this->conn->prepare("DELETE FROM user_tokens WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error deleting remember token: ' . $e->getMessage());
            return false;
        }
    }
}