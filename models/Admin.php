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
                SELECT b.*, u.username, p.title as play_title
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN plays p ON b.play_id = p.play_id
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

    public function getMonthlyRevenueData() {
        try {
            // Get revenue data for the last 6 months
            $stmt = $this->conn->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(amount) as revenue
                FROM bookings
                WHERE status = 'Paid'
                AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $months = [];
            $revenues = [];
            
            while ($row = $result->fetch_assoc()) {
                $dateObj = DateTime::createFromFormat('Y-m', $row['month']);
                $months[] = $dateObj->format('M Y'); // Format as "Jan 2025"
                $revenues[] = (float)$row['revenue'];
            }
            
            return [
                'labels' => $months,
                'data' => $revenues
            ];
        } catch (Exception $e) {
            error_log("Error getting monthly revenue data: " . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }
    
    public function getBookingsByStatusData() {
        try {
            // Get booking counts by status
            $stmt = $this->conn->prepare("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM bookings
                GROUP BY status
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $statuses = [];
            $counts = [];
            
            while ($row = $result->fetch_assoc()) {
                $statuses[] = $row['status'];
                $counts[] = (int)$row['count'];
            }
            
            return [
                'labels' => $statuses,
                'data' => $counts
            ];
        } catch (Exception $e) {
            error_log("Error getting bookings by status data: " . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    public function storeRememberToken($admin_id, $token, $expires) {
        try {
            // First, delete any existing tokens for this admin
            $deleteStmt = $this->conn->prepare("DELETE FROM admin_tokens WHERE admin_id = ?");
            $deleteStmt->bind_param("i", $admin_id);
            $deleteStmt->execute();
            
            // Now insert the new token
            $expires_at = date('Y-m-d H:i:s', $expires);
            $stmt = $this->conn->prepare("INSERT INTO admin_tokens (admin_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $admin_id, $token, $expires_at);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error storing admin remember token: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getAdminByRememberToken($token) {
        try {
            $current_time = date('Y-m-d H:i:s');
            $stmt = $this->conn->prepare("
                SELECT a.* 
                FROM admins a
                JOIN admin_tokens t ON a.admin_id = t.admin_id
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
            error_log('Error getting admin by remember token: ' . $e->getMessage());
            return null;
        }
    }
    
    public function deleteRememberToken($admin_id, $token = null) {
        try {
            if ($token) {
                // Delete specific token
                $stmt = $this->conn->prepare("DELETE FROM admin_tokens WHERE admin_id = ? AND token = ?");
                $stmt->bind_param("is", $admin_id, $token);
            } else {
                // Delete all tokens for admin
                $stmt = $this->conn->prepare("DELETE FROM admin_tokens WHERE admin_id = ?");
                $stmt->bind_param("i", $admin_id);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error deleting admin remember token: ' . $e->getMessage());
            return false;
        }
    }
}