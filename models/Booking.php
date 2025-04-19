<?php
class Booking {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createBooking($user_id, $play_id, $theater_id, $seat_id, $expires_at, $amount, $schedule_date) {
        $sql = "INSERT INTO bookings (user_id, play_id, theater_id, seat_id, status, expires_at, amount, schedule_date) 
                VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssds", $user_id, $play_id, $theater_id, $seat_id, $expires_at, $amount, $schedule_date);
        return $stmt->execute();
    }
    
    public function getBookingsByUserId($user_id) {
        $sql = "SELECT b.*, p.title as play_title, t.name as theater_name, 
                s.date as schedule_date, s.start_time, s.end_time,
                sm.seat_type, sp.price
                FROM bookings b
                JOIN plays p ON b.play_id = p.play_id
                JOIN theaters t ON b.theater_id = t.theater_id
                LEFT JOIN schedules s ON b.play_id = s.play_id AND b.schedule_date = s.date
                JOIN seat_maps sm ON b.theater_id = sm.theater_id AND b.seat_id = sm.seat_id
                JOIN seat_prices sp ON b.theater_id = sp.theater_id AND sm.seat_type = sp.seat_type
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        return $bookings;
    }

    public function getBookingById($booking_id) {
        $sql = "SELECT * FROM bookings WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function deleteBooking($booking_id) {
        $sql = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $result = $stmt->execute();
        
        return $result;
    }

    public function getExpiredBookings() {
        $current_time = date('Y-m-d H:i:s');
        
        $sql = "SELECT b.* FROM bookings b 
                WHERE b.status = 'Pending' 
                AND b.expires_at < ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $expired_bookings = [];
        while ($row = $result->fetch_assoc()) {
            $expired_bookings[] = $row;
        }
        
        return $expired_bookings;
    }
    
    public function updateBookingStatus($booking_id, $status) {
        $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $booking_id);
        return $stmt->execute();
    }

    public function checkPlayHasBookings($play_id) {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] > 0);
    }

    public function getPaginatedBookings($page = 1, $per_page = 10) {
        try {
            // Calculate offset for pagination
            $offset = ($page - 1) * $per_page;
            
            // Count total bookings
            $countStmt = $this->conn->prepare("SELECT COUNT(*) as total FROM bookings");
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            
            // Get bookings with pagination
            $stmt = $this->conn->prepare("
                SELECT b.*, u.username, p.title as play_title, t.name as theater_name
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN plays p ON b.play_id = p.play_id
                JOIN theaters t ON b.theater_id = t.theater_id
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $bookings = [];
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            
            // Return both bookings and pagination data
            return [
                'bookings' => $bookings,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $per_page,
                    'current_page' => $page,
                    'last_page' => ceil($total / $per_page)
                ]
            ];
        } catch (Exception $e) {
            error_log("Error in getPaginatedBookings: " . $e->getMessage());
            return [
                'bookings' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $per_page,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ];
        }
    }
    
    public function getBookingDetailsById($booking_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT b.*, p.title as play_title, t.name as theater_name, 
                       s.date as schedule_date, s.start_time, s.end_time, 
                       sm.seat_type, sp.price
                FROM bookings b
                JOIN plays p ON b.play_id = p.play_id
                JOIN theaters t ON b.theater_id = t.theater_id
                LEFT JOIN schedules s ON b.play_id = s.play_id AND b.schedule_date = s.date
                LEFT JOIN seat_maps sm ON b.theater_id = sm.theater_id AND b.seat_id = sm.seat_id
                LEFT JOIN seat_prices sp ON b.theater_id = sp.theater_id AND sm.seat_type = sp.seat_type
                WHERE b.booking_id = ?
            ");
            
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error getting booking details: " . $e->getMessage());
            return null;
        }
    }
    
    public function getBookedSeatsByPlay($play_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT seat_id FROM bookings 
                WHERE play_id = ? AND (status = 'Paid' OR status = 'Pending')
            ");
            
            $stmt->bind_param("s", $play_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $bookedSeats = [];
            while ($row = $result->fetch_assoc()) {
                $bookedSeats[] = $row['seat_id'];
            }
            
            return $bookedSeats;
        } catch (Exception $e) {
            error_log("Error getting booked seats: " . $e->getMessage());
            return [];
        }
    }
}