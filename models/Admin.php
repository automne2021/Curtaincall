<?php
class Admin {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                // Remove password before returning
                unset($admin['password']);
                return $admin;
            }
        }
        
        return false;
    }
    
    // Get statistics for dashboard
    public function getDashboardStats() {
        // Users count
        $sql_users = "SELECT COUNT(*) as total FROM users";
        $result_users = $this->conn->query($sql_users);
        $total_users = $result_users->fetch_assoc()['total'];
        
        // Plays count
        $sql_plays = "SELECT COUNT(*) as total FROM plays";
        $result_plays = $this->conn->query($sql_plays);
        $total_plays = $result_plays->fetch_assoc()['total'];
        
        // Bookings count
        $sql_bookings = "SELECT COUNT(*) as total FROM bookings";
        $result_bookings = $this->conn->query($sql_bookings);
        $total_bookings = $result_bookings->fetch_assoc()['total'];
        
        // Theaters count
        $sql_theaters = "SELECT COUNT(*) as total FROM theaters";
        $result_theaters = $this->conn->query($sql_theaters);
        $total_theaters = $result_theaters->fetch_assoc()['total'];
        
        return [
            'total_users' => $total_users,
            'total_plays' => $total_plays,
            'total_bookings' => $total_bookings,
            'total_theaters' => $total_theaters
        ];
    }
    
    // Get recent bookings for dashboard
    public function getRecentBookings($limit = 5) {
        $sql = "SELECT b.*, u.username, p.title as play_title 
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN plays p ON b.play_id = p.play_id
                ORDER BY b.created_at DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        return $bookings;
    }
}