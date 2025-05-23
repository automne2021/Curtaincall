<?php
require_once 'models/Play.php';
require_once 'models/Theater.php';

class PlayController
{
    private $conn;
    private $playModel;
    private $theaterModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
    }

    public function index()
    {        
        $theater_id = isset($_GET['theater_id']) && $_GET['theater_id'] !== '' ? $_GET['theater_id'] : null;
        $sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'date';
        $sort_dir = isset($_GET['dir']) ? strtolower($_GET['dir']) : 'desc';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $plays_per_page = 8;

        // Get the data from models
        $result = $this->playModel->getPlaysByTheater($theater_id, $sort_field, $sort_dir, $page, $plays_per_page);
        $theaters_result = $this->theaterModel->getAllTheaters();

        // Get the theater name if theater_id is provided
        $theater_name = "Tất cả nhà hát kịch";
        if ($theater_id) {
            $theater = $this->theaterModel->getTheaterById($theater_id);
            if ($theater) {
                $theater_name = $theater['name'];
            }
        }

        // Calculate pagination
        $total_plays = $this->playModel->getTotalPlays($theater_id);
        $total_pages = ceil($total_plays / $plays_per_page);

        // Make variables available to the view
        $GLOBALS['sort_field'] = $sort_field;
        $GLOBALS['sort_dir'] = $sort_dir;
        $GLOBALS['theater_id'] = $theater_id;

        // Also extract them to local scope
        extract([
            'theater_id' => $theater_id,
            'sort_field' => $sort_field,
            'sort_dir' => $sort_dir,
            'page' => $page,
            'total_pages' => $total_pages
        ]);

        // Load the views
        include 'views/layouts/header.php';
        include 'views/plays/index.php';
        include 'views/layouts/footer.php';
    }

    public function view($id = null)
    {
        // Get the play ID from the parameter or from the query string
        $play_id = $id ?? $_GET['play_id'] ?? null;

        if (!$play_id) {
            // Redirect to the plays listing if no ID is provided
            header("Location: index.php?route=play");
            exit;
        }

        // Get the play details
        $play = $this->playModel->getPlayById($play_id);

        if (!$play) {
            // Redirect to the plays listing if the play doesn't exist
            header("Location: index.php?route=play");
            exit;
        }

        // Get theaters for the navigation menu
        $theaters_result = $this->theaterModel->getAllTheaters();
        
        // Get schedules
        $schedules_sql = "SELECT * FROM schedules WHERE play_id = ? AND date >= CURDATE() ORDER BY date, start_time";
        $stmt = $this->conn->prepare($schedules_sql);
        $stmt->bind_param("s", $play_id);
        $stmt->execute();
        $schedules_result = $stmt->get_result();
        
        // Process schedules
        $dates = [];
        $schedules = [];
        if ($schedules_result->num_rows > 0) {
            while ($schedule = $schedules_result->fetch_assoc()) {
                $date = $schedule['date'];
                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }
                $schedules[] = $schedule;
            }
        }
        
        // Pass the processed data to the view
        $has_schedules = $schedules_result->num_rows > 0;

        // Load the views
        include 'views/layouts/header.php';
        include 'views/plays/details.php';
        include 'views/layouts/footer.php';
    }
}
