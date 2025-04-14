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
}
