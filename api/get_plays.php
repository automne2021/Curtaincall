<?php
include 'db_config.php';
header('Content-Type: application/json');


$theater_id = isset($_GET['theater_id']) && $_GET['theater_id'] !== '' ? $_GET['theater_id'] : null;
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$sort_dir = isset($_GET['dir']) ? $_GET['dir'] : 'desc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$plays_per_page = 8;
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
        $orderBy = "IFNULL(MIN(s.date), '9999-12-31') " . strtoupper($sort_dir);
        break;
}

if ($theater_id !== null) {
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

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $theater_id, $plays_per_page, $offset); // Use "sii" for string + integers
} else {
    // Show all plays if no theater is selected
    $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name,
            MIN(s.date) as date, MIN(s.start_time) as start_time, MIN(s.end_time) as end_time
            FROM plays p
            JOIN theaters t ON p.theater_id = t.theater_id
            LEFT JOIN seat_prices sp ON p.theater_id = sp.theater_id
            LEFT JOIN schedules s ON p.play_id = s.play_id
            GROUP BY p.play_id, p.title, p.theater_id, t.name
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $plays_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$plays = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format data for display
        $row['formatted_price'] = number_format($row['min_price'], 0, ',', '.');
        $row['formatted_date'] = date("d \\t\h\á\\n\g m, Y", strtotime($row['date']));
        $plays[] = $row;
    }
}

// Count total plays query (for pagination)
if ($theater_id !== null) {
    $count_sql = "SELECT COUNT(*) as total FROM plays p WHERE p.theater_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $theater_id); // Use "i" for integer
} else {
    $count_sql = "SELECT COUNT(*) as total FROM plays p";
    $count_stmt = $conn->prepare($count_sql);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_plays = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_plays / $plays_per_page);

echo json_encode([
    'plays' => $plays,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_plays' => $total_plays,
        'has_more' => ($page < $total_pages)
    ]
]);

$conn->close();
