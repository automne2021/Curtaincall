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
    
    public function getScheduleByPlayId($play_id) {
        $sql = "SELECT * FROM schedules WHERE play_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    public function createSchedule($schedule_data) {
        $sql = "INSERT INTO schedules (play_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $schedule_data['play_id'], 
            $schedule_data['date'], 
            $schedule_data['start_time'], 
            $schedule_data['end_time']
        );
        
        return $stmt->execute();
    }
    
    public function updateSchedule($schedule_data) {
        $sql = "UPDATE schedules SET date = ?, start_time = ?, end_time = ? WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $schedule_data['date'], 
            $schedule_data['start_time'], 
            $schedule_data['end_time'],
            $schedule_data['play_id']
        );
        
        return $stmt->execute();
    }

    public function deleteSchedulesByPlayId($play_id) {
        $sql = "DELETE FROM schedules WHERE play_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        return $stmt->execute();
    }
}