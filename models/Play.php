<?php
// filepath: c:\Users\VY\Downloads\curtaincall\models\Play.php

class Play
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getHotPlays($limit = 6)
    {
        $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name 
            FROM plays p
            JOIN seat_prices sp ON p.theater_id = sp.theater_id
            JOIN theaters t ON p.theater_id = t.theater_id
            LEFT JOIN schedules s ON p.play_id = s.play_id
            WHERE s.date >= CURDATE()
            GROUP BY p.play_id
            ORDER BY p.views DESC, s.date ASC
            LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function getUpComingPlays($limit = 8, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name, s.date
                FROM plays p
                JOIN seat_prices sp ON p.theater_id = sp.theater_id
                JOIN theaters t ON p.theater_id = t.theater_id
                INNER JOIN schedules s ON p.play_id = s.play_id
                WHERE s.date IS NOT NULL AND s.date >= CURDATE()
                GROUP BY p.play_id
                ORDER BY s.date ASC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function getPlaysByTheater($theater_id = null, $sort_field = 'date', $sort_dir = 'DESC', $page = 1, $plays_per_page = 8)
    {
        $offset = ($page - 1) * $plays_per_page;

        // Define order by clause based on sort parameter
        switch ($sort_field) {
            case 'name':
                $orderBy = "p.title " . strtoupper($sort_dir);
                break;
            case 'price':
                $orderBy = "min_price " . strtoupper($sort_dir);
                break;
            case 'date':
                // Use IFNULL for date sorting to handle NULL dates
                $orderBy = "IFNULL(MIN(s.date), '9999-12-31') " . strtoupper($sort_dir);
                break;
            default:
                $orderBy = "p.created_at " . strtoupper($sort_dir);
                break;
        }

        // Prepare query based on if theater_id is provided
        if ($theater_id) {
            // CRITICAL FIX: Remove type casting and use string parameter binding for theater_id
            $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name,
                    MIN(s.date) as date, MIN(s.start_time) as start_time, MIN(s.end_time) as end_time
                    FROM plays p
                    JOIN theaters t ON p.theater_id = t.theater_id
                    LEFT JOIN seat_prices sp ON p.theater_id = sp.theater_id
                    LEFT JOIN schedules s ON p.play_id = s.play_id
                    WHERE p.theater_id = ?
                    GROUP BY p.play_id, p.title, p.theater_id, t.name
                    ORDER BY {$orderBy}
                    LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $theater_id, $plays_per_page, $offset); // Use "s" for string
        } else {
            $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name,
                    MIN(s.date) as date, MIN(s.start_time) as start_time, MIN(s.end_time) as end_time
                    FROM plays p
                    JOIN theaters t ON p.theater_id = t.theater_id
                    LEFT JOIN seat_prices sp ON p.theater_id = sp.theater_id
                    LEFT JOIN schedules s ON p.play_id = s.play_id
                    GROUP BY p.play_id, p.title, p.theater_id, t.name
                    ORDER BY {$orderBy}
                    LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $plays_per_page, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function getTotalPlays($theater_id = null)
    {
        if ($theater_id) {
            // Convert to integer explicitly
            $theater_id = (int)$theater_id;

            $sql = "SELECT COUNT(*) as total FROM plays p WHERE p.theater_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $theater_id);  // Change "s" to "i" for integer
        } else {
            $sql = "SELECT COUNT(*) as total FROM plays p";
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];

        return $total;
    }

    public function getPlayById($play_id)
    {
        $sql = "SELECT p.*, t.name as theater_name, t.location as theater_location,
                MIN(sp.price) as min_price,
                MIN(s.date) as date, MIN(s.start_time) as start_time, MIN(s.end_time) as end_time 
                FROM plays p 
                JOIN theaters t ON p.theater_id = t.theater_id 
                JOIN seat_maps sm ON p.theater_id = sm.theater_id
                JOIN seat_prices sp ON sm.theater_id = sp.theater_id AND sm.seat_type = sp.seat_type
                LEFT JOIN schedules s ON p.play_id = s.play_id AND s.date >= CURDATE()
                WHERE p.play_id = ?
                GROUP BY p.play_id, p.title, p.theater_id, t.name, t.location";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function createPlay($play_data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO plays (play_id, title, description, theater_id, image, views) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param(
                "sssssi", 
                $play_data['play_id'], 
                $play_data['title'], 
                $play_data['description'], 
                $play_data['theater_id'], 
                $play_data['image'],
                $play_data['views']
            );
            
            $stmt->execute();
            
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error creating play: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePlay($play_data) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE plays 
                SET title = ?, description = ?, theater_id = ?, image = ?
                WHERE play_id = ?
            ");
            
            $stmt->bind_param(
                "sssss", 
                $play_data['title'], 
                $play_data['description'], 
                $play_data['theater_id'], 
                $play_data['image'], 
                $play_data['play_id']
            );
            
            $stmt->execute();
            
            return $stmt->affected_rows >= 0;
        } catch (Exception $e) {
            error_log("Error updating play: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePlay($play_id) {
        try {
            // Begin transaction
            $this->conn->begin_transaction();
            
            // Delete related seats
            $seatsStmt = $this->conn->prepare("DELETE FROM seats WHERE play_id = ?");
            $seatsStmt->bind_param("s", $play_id);
            $seatsStmt->execute();
            
            // Delete schedules for this play
            $scheduleStmt = $this->conn->prepare("DELETE FROM schedules WHERE play_id = ?");
            $scheduleStmt->bind_param("s", $play_id);
            $scheduleStmt->execute();
            
            // Delete the play
            $playStmt = $this->conn->prepare("DELETE FROM plays WHERE play_id = ?");
            $playStmt->bind_param("s", $play_id);
            $playStmt->execute();
            $result = $playStmt->affected_rows > 0;
            
            $this->conn->commit();
            return $result;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error deleting play: " . $e->getMessage());
            return false;
        }
    }

    public function getPopularPlays($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT p.*, COUNT(b.booking_id) as booking_count 
                FROM plays p
                LEFT JOIN bookings b ON p.play_id = b.play_id
                GROUP BY p.play_id
                ORDER BY booking_count DESC
                LIMIT ?
            ");
            
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $plays = [];
            while ($row = $result->fetch_assoc()) {
                $plays[] = $row;
            }
            
            return $plays;
        } catch (Exception $e) {
            error_log("Error fetching popular plays: " . $e->getMessage());
            return [];
        }
    }

    public function getAllPlays($page = 1, $per_page = 10) {
        try {
            // Calculate offset for pagination
            $offset = ($page - 1) * $per_page;
            
            // Query to count total plays
            $countStmt = $this->conn->prepare("SELECT COUNT(*) as total FROM plays");
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            
            // Modified query to get duration from schedules table with pagination
            $stmt = $this->conn->prepare("
                SELECT p.play_id, p.title, p.description, p.image, 
                       p.theater_id, p.views, 
                       t.name as theater_name,
                       TIMEDIFF(s.end_time, s.start_time) as duration
                FROM plays p
                LEFT JOIN theaters t ON p.theater_id = t.theater_id
                LEFT JOIN schedules s ON p.play_id = s.play_id
                GROUP BY p.play_id
                ORDER BY p.play_id DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bind_param("ii", $per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // DEBUG
            if ($result->num_rows == 0) {
                error_log("Warning: No plays found in database");
            }
            
            $plays = [];
            while ($row = $result->fetch_assoc()) {
                $duration = 'N/A';
                if ($row['duration']) {
                    $time_parts = explode(':', $row['duration']);
                    if (count($time_parts) === 3) {
                        $hours = (int)$time_parts[0];
                        $minutes = (int)$time_parts[1];
                        $duration = ($hours * 60) + $minutes;
                    }
                }
                
                $plays[] = [
                    'play_id' => $row['play_id'],
                    'title' => $row['title'],
                    'theater_id' => $row['theater_id'],
                    'theater_name' => $row['theater_name'],
                    'duration' => $duration,
                    'image' => $row['image'],
                    'description' => $row['description'],
                    'views' => $row['views']
                ];
            }
            
            // Return both plays and pagination data
            return [
                'plays' => $plays,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $per_page,
                    'current_page' => $page,
                    'last_page' => ceil($total / $per_page)
                ]
            ];
        } catch (Exception $e) {
            error_log("Error in getAllPlays: " . $e->getMessage());
            return [
                'plays' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $per_page,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ];
        }
    }

    public function searchPlays($query, $limit = null) {
        try {
            $searchTerm = '%' . $query . '%';
            
            $limitClause = '';
            if ($limit !== null) {
                $limitClause = "LIMIT ?";
            }
            
            $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name, 
                    MIN(s.date) as date
                    FROM plays p
                    JOIN theaters t ON p.theater_id = t.theater_id
                    LEFT JOIN seat_prices sp ON p.theater_id = sp.theater_id
                    LEFT JOIN schedules s ON p.play_id = s.play_id AND s.date >= CURDATE()
                    WHERE p.title LIKE ? OR p.description LIKE ? OR t.name LIKE ?
                    GROUP BY p.play_id
                    ORDER BY 
                        CASE 
                            WHEN p.title LIKE ? THEN 1
                            WHEN t.name LIKE ? THEN 2
                            ELSE 3
                        END,
                        s.date ASC
                    $limitClause";
            
            $stmt = $this->conn->prepare($sql);
            
            if ($limit !== null) {
                $stmt->bind_param("ssssi", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit);
            } else {
                $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $plays = [];
            while ($row = $result->fetch_assoc()) {
                $plays[] = $row;
            }
            
            return $plays;
        } catch (Exception $e) {
            error_log("Error searching plays: " . $e->getMessage());
            return [];
        }
    }
}
