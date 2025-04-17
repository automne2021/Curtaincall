<?php
class Admin {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAdminByUsername($username) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error fetching admin: " . $e->getMessage());
            return null;
        }
    }
    
    public function getDashboardStats() {
        try {
            // Get total plays
            $playStmt = $this->conn->prepare("SELECT COUNT(*) as count FROM plays");
            $playStmt->execute();
            $playResult = $playStmt->get_result();
            $total_plays = $playResult->fetch_assoc()['count'] ?? 0;
            error_log("Total plays query result: " . $total_plays);
            // Get total bookings
            $bookingStmt = $this->conn->prepare("SELECT COUNT(*) as count FROM bookings");
            $bookingStmt->execute();
            $bookingResult = $bookingStmt->get_result();
            $total_bookings = $bookingResult->fetch_assoc()['count'] ?? 0;
            
            // Get total users
            $userStmt = $this->conn->prepare("SELECT COUNT(*) as count FROM users");
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            $total_users = $userResult->fetch_assoc()['count'] ?? 0;
            
            // Get monthly revenue
            $revenueStmt = $this->conn->prepare("
                SELECT SUM(amount) as total 
                FROM bookings 
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
            ");
            $revenueStmt->execute();
            $revenueResult = $revenueStmt->get_result();
            $monthly_revenue = $revenueResult->fetch_assoc()['total'] ?? 0;
            
            return [
                'total_plays' => $total_plays,
                'total_bookings' => $total_bookings,
                'total_users' => $total_users,
                'monthly_revenue' => $monthly_revenue
            ];
        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            return [
                'total_plays' => $e->getMessage(),
                'total_bookings' => 0,
                'total_users' => 0,
                'monthly_revenue' => 0
            ];
        }
    }
    
    public function getRecentBookings($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT b.*, u.username, p.title as play_title, s.datetime as schedule_datetime,
                       COUNT(bs.seat_id) as seat_count
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN plays p ON b.play_id = p.play_id
                JOIN schedules s ON b.schedule_id = s.schedule_id
                JOIN booking_seats bs ON b.booking_id = bs.booking_id
                GROUP BY b.booking_id
                ORDER BY b.created_at DESC
                LIMIT ?
            ");
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $bookings = [];
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            
            return $bookings;
        } catch (Exception $e) {
            error_log("Error fetching recent bookings: " . $e->getMessage());
            return [];
        }
    }
}