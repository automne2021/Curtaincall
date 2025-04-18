<?php
require_once '../config/database.php';

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

    $sql = "SELECT play_id, title 
            FROM plays 
            WHERE title LIKE '$search%'
            ORDER BY title
            LIMIT $limit";

    $result = $conn->query($sql);

    $plays = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $plays[] = [
                'play_id' => $row['play_id'],
                'title' => $row['title']
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($plays);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}
