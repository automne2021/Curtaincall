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
        // Convert to integer explicitly
        $theater_id = (int)$theater_id;
        
        $sql = "SELECT * FROM theaters WHERE theater_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $theater_id);  // Change "s" to "i" for integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }
}
