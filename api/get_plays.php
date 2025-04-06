<?php
include 'db_config.php';
header('Content-Type: application/json');

// Get parameters
$theater_id = isset($_GET['theater_id']) ? $_GET['theater_id'] : '';
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
        $orderBy = "p.date " . strtoupper($sort_dir);
        break;
}

// Prepare query based on if theater_id is provided
if (!empty($theater_id)) {
    $sql = "SELECT p.*, MIN(sp.price) as min_price, t.name as theater_name
            FROM plays p
            JOIN seat_prices sp ON p.theater_id = sp.theater_id
            JOIN theaters t ON p.theater_id = t.theater_id
            WHERE p.theater_id = ?
            GROUP BY p.play_id
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
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
if (!empty($theater_id)) {
    $count_sql = "SELECT COUNT(*) as total FROM plays p WHERE p.theater_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $theater_id);
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
