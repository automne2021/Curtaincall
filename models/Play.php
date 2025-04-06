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
                LEFT JOIN schedules s ON p.play_id = s.play_id
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
            default:
                $orderBy = "s.date " . strtoupper($sort_dir);
                break;
        }

        // Prepare query based on if theater_id is provided
        if ($theater_id) {
            $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name
                    FROM plays p
                    JOIN seat_prices sp ON p.theater_id = sp.theater_id
                    JOIN theaters t ON p.theater_id = t.theater_id
                    LEFT JOIN schedules s ON p.play_id = s.play_id
                    WHERE p.theater_id = ?
                    GROUP BY p.play_id
                    ORDER BY {$orderBy}
                    LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $theater_id, $plays_per_page, $offset);
        } else {
            // Show all plays if no theater is selected
            $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name
                    FROM plays p
                    JOIN seat_prices sp ON p.theater_id = sp.theater_id
                    JOIN theaters t ON p.theater_id = t.theater_id
                    GROUP BY p.play_id
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
            $sql = "SELECT COUNT(*) as total FROM plays p WHERE p.theater_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $theater_id);
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
        $sql = "SELECT p.*, t.name as theater_name, t.location as theater_location 
                FROM plays p 
                JOIN theaters t ON p.theater_id = t.theater_id 
                WHERE p.play_id = ?";
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
