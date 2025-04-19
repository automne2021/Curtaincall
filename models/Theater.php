<?php
// filepath: c:\Users\VY\Downloads\curtaincall\models\Theater.php

class Theater
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllTheaters()
    {
        $sql = "SELECT * FROM theaters ORDER BY name";
        $result = $this->conn->query($sql);

        return $result;
    }

    public function getTheaterById($theater_id)
    {
        $sql = "SELECT * FROM theaters WHERE theater_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $theater_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Create a new theater
    public function createTheater($theater_data) {
        try {
            $sql = "INSERT INTO theaters (theater_id, name, location) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", 
                $theater_data['theater_id'], 
                $theater_data['name'], 
                $theater_data['location']
            );
            
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error creating theater: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateTheater($theater_data) {
        try {
            $sql = "UPDATE theaters SET name = ?, location = ? WHERE theater_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", 
                $theater_data['name'], 
                $theater_data['location'], 
                $theater_data['theater_id']
            );
            
            $stmt->execute();
            return $stmt->affected_rows >= 0;
        } catch (Exception $e) {
            error_log("Error updating theater: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete a theater
    public function deleteTheater($theater_id) {
        try {
            $sql = "DELETE FROM theaters WHERE theater_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $theater_id);
            
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error deleting theater: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if a theater has associated plays
    public function hasPlays($theater_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM plays WHERE theater_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $theater_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'] > 0;
        } catch (Exception $e) {
            error_log("Error checking if theater has plays: " . $e->getMessage());
            return true; // Return true as a safety measure
        }
    }
    
    // Get all theaters with pagination
    public function getPaginatedTheaters($page = 1, $per_page = 10) {
        try {
            // Calculate offset for pagination
            $offset = ($page - 1) * $per_page;
            
            // Count total theaters
            $countStmt = $this->conn->prepare("SELECT COUNT(*) as total FROM theaters");
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            
            // Get theaters with pagination
            $stmt = $this->conn->prepare("
                SELECT * FROM theaters
                ORDER BY name ASC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $theaters = [];
            while ($row = $result->fetch_assoc()) {
                $theaters[] = $row;
            }
            
            // Return both theaters and pagination data
            return [
                'theaters' => $theaters,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $per_page,
                    'current_page' => $page,
                    'last_page' => ceil($total / $per_page)
                ]
            ];
        } catch (Exception $e) {
            error_log("Error in getPaginatedTheaters: " . $e->getMessage());
            return [
                'theaters' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $per_page,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ];
        }
    }
}
