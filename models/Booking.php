<?php
class Booking {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createBooking($user_id, $play_id, $theater_id, $seat_id, $expires_at, $amount) {
        $sql = "INSERT INTO bookings (user_id, play_id, theater_id, seat_id, status, expires_at, amount) 
                VALUES (?, ?, ?, ?, 'Pending', ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssd", $user_id, $play_id, $theater_id, $seat_id, $expires_at, $amount);
        return $stmt->execute();
    }
    
    public function getBookingsByUserId($user_id) {
        $sql = "SELECT b.*, p.title as play_title, t.name as theater_name, 
                s.date as schedule_date, s.start_time, s.end_time,
                sm.seat_type, sp.price
                FROM bookings b
                JOIN plays p ON b.play_id = p.play_id
                JOIN theaters t ON b.theater_id = t.theater_id
                JOIN schedules s ON b.play_id = s.play_id
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
}