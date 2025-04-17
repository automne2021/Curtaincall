<?php
class Seat {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getSeatMapByTheater($theater_id) {
        $sql = "SELECT * FROM seat_maps WHERE theater_id = ? ORDER BY seat_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $theater_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $seatMap = [];
        while ($row = $result->fetch_assoc()) {
            $seatMap[] = $row;
        }
        
        return $seatMap;
    }
    
    public function getSeatAvailability($play_id, $date, $time) {
        // First, ensure seats exist for this play
        $this->createSeatsIfNotExists($play_id);
        
        $sql = "SELECT * FROM seats WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $seatAvailability = [];
        while ($row = $result->fetch_assoc()) {
            $seatAvailability[$row['seat_id']] = $row['status'];
        }
        
        return $seatAvailability;
    }
    
    private function createSeatsIfNotExists($play_id) {
        // Get play info to determine theater_id
        $sql = "SELECT theater_id FROM plays WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        $play = $result->fetch_assoc();
        $theater_id = $play['theater_id'];
        
        // Get seat map for this theater
        $sql = "SELECT seat_id FROM seat_maps WHERE theater_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $theater_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // For each seat in the map, create a corresponding entry in the seats table if it doesn't exist
        while ($seat = $result->fetch_assoc()) {
            $seat_id = $seat['seat_id'];
            
            // Check if this seat already exists for this play
            $checkSql = "SELECT * FROM seats WHERE theater_id = ? AND play_id = ? AND seat_id = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bind_param("sss", $theater_id, $play_id, $seat_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows === 0) {
                // Seat doesn't exist, create it
                $insertSql = "INSERT INTO seats (theater_id, play_id, seat_id, status) VALUES (?, ?, ?, 'Available')";
                $insertStmt = $this->conn->prepare($insertSql);
                $insertStmt->bind_param("sss", $theater_id, $play_id, $seat_id);
                $insertStmt->execute();
            }
        }
        
        return true;
    }
    
    public function getSeatPrices($theater_id) {
        $sql = "SELECT * FROM seat_prices WHERE theater_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $theater_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[$row['seat_type']] = $row['price'];
        }
        
        return $prices;
    }
    
    public function getSelectedSeatsInfo($theater_id, $selected_seats) {
        $result = [];
        
        foreach ($selected_seats as $seat_id) {
            // Get seat type
            $sql = "SELECT seat_type FROM seat_maps WHERE theater_id = ? AND seat_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $theater_id, $seat_id);
            $stmt->execute();
            $seatTypeResult = $stmt->get_result();
            
            if ($seatTypeResult->num_rows > 0) {
                $seatTypeRow = $seatTypeResult->fetch_assoc();
                $seat_type = $seatTypeRow['seat_type'];
                
                // Get price for this seat type
                $sql = "SELECT price FROM seat_prices WHERE theater_id = ? AND seat_type = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $theater_id, $seat_type);
                $stmt->execute();
                $priceResult = $stmt->get_result();
                
                if ($priceResult->num_rows > 0) {
                    $priceRow = $priceResult->fetch_assoc();
                    $result[] = [
                        'seat_id' => $seat_id,
                        'seat_type' => $seat_type,
                        'price' => $priceRow['price']
                    ];
                }
            }
        }
        
        return $result;
    }
    
    public function updateSeatStatus($play_id, $theater_id, $seat_id, $status) {
        $sql = "UPDATE seats SET status = ? WHERE play_id = ? AND theater_id = ? AND seat_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $status, $play_id, $theater_id, $seat_id);
        return $stmt->execute();
    }

    public function getSeatPrice($theater_id, $seat_id) {
        try {
            // Get the seat type from seat_maps
            $seatTypeStmt = $this->conn->prepare("
                SELECT seat_type FROM seat_maps 
                WHERE theater_id = ? AND seat_id = ?
            ");
            $seatTypeStmt->bind_param("ss", $theater_id, $seat_id);
            $seatTypeStmt->execute();
            $seatTypeResult = $seatTypeStmt->get_result();
            
            if ($seatTypeResult->num_rows === 0) {
                return 0; // Seat not found
            }
            
            $seatType = $seatTypeResult->fetch_assoc()['seat_type'];
            
            // Get the price for this seat type
            $priceStmt = $this->conn->prepare("
                SELECT price FROM seat_prices 
                WHERE theater_id = ? AND seat_type = ?
            ");
            $priceStmt->bind_param("ss", $theater_id, $seatType);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            
            if ($priceResult->num_rows === 0) {
                return 0; // Price not found
            }
            
            return $priceResult->fetch_assoc()['price'];
        } catch (Exception $e) {
            error_log("Error getting seat price: " . $e->getMessage());
            return 0;
        }
    }
}