<?php
class Schedule {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getSchedulesByPlayId($play_id) {
        $sql = "SELECT * FROM schedules WHERE play_id = ? AND date >= CURDATE() ORDER BY date ASC, start_time ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $schedules = [];
        while ($row = $result->fetch_assoc()) {
            $schedules[] = $row;
        }
        
        return $schedules;
    }

    public function deleteSchedulesByPlayId($play_id) {
        $sql = "DELETE FROM schedules WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $play_id);
        return $stmt->execute();
    }
}