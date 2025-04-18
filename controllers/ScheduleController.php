<?php
require_once 'models/Schedule.php';
require_once 'models/Play.php';
require_once 'models/Theater.php';

class ScheduleController
{
    private $conn;
    private $scheduleModel;
    private $playModel;
    private $theaterModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->scheduleModel = new Schedule($conn);
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
    }

    public function index()
    {
        // Lấy tham số lọc từ URL
        $play_id = isset($_GET['play_id']) ? (int)$_GET['play_id'] : null;
        $theater_id = isset($_GET['theater_id']) ? (int)$_GET['theater_id'] : null;
        $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d');
        $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d', strtotime('+30 days'));

        $schedules = $this->scheduleModel->getSchedulesByPlayId($play_id, $theater_id, $date_from, $date_to);

        $plays = $this->playModel->getAllPlays();
        $theaters = $this->theaterModel->getAllTheaters();

        include 'views/layouts/header.php';
        include 'views/plays/details.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Hiển thị chi tiết lịch chiếu
     */
    public function view()
    {
        $schedule_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$schedule_id) {
            $_SESSION['error_message'] = 'Không tìm thấy lịch chiếu';
            header('Location: index.php?route=schedule');
            exit;
        }

        $schedule = $this->scheduleModel->getSchedulesByPlayId($schedule_id);

        if (!$schedule) {
            $_SESSION['error_message'] = 'Không tìm thấy lịch chiếu';
            header('Location: index.php?route=schedule');
            exit;
        }

        $play = $this->playModel->getPlayById($schedule['play_id']);
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);

        include 'views/layouts/header.php';
        include 'views/schedules/view.php';
        include 'views/layouts/footer.php';
    }

    /**
     * API để lấy lịch chiếu dựa trên vở kịch và ngày
     */
    public function getSchedulesByPlay()
    {
        header('Content-Type: application/json');

        $play_id = isset($_GET['play_id']) ? (int)$_GET['play_id'] : null;
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        if (!$play_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng chọn vở kịch'
            ]);
            exit;
        }

        $schedules = $this->scheduleModel->getSchedulesByPlayId($play_id, $date);

        echo json_encode([
            'success' => true,
            'data' => $schedules
        ]);
        exit;
    }

    /**
     * Hiển thị lịch chiếu sắp tới theo vở kịch
     */
    public function playSchedules()
    {
        $play_id = isset($_GET['play_id']) ? (int)$_GET['play_id'] : null;

        if (!$play_id) {
            $_SESSION['error_message'] = 'Vui lòng chọn vở kịch';
            header('Location: index.php?route=play');
            exit;
        }

        $play = $this->playModel->getPlayById($play_id);

        if (!$play) {
            $_SESSION['error_message'] = 'Không tìm thấy vở kịch';
            header('Location: index.php?route=play');
            exit;
        }

        $today = date('Y-m-d');
        $next_month = date('Y-m-d', strtotime('+30 days'));

        // Lấy lịch chiếu cho vở kịch này từ hôm nay đến 30 ngày sau
        $schedules = $this->scheduleModel->getSchedulesByPlayId($play_id, $today, $next_month);

        // Sắp xếp lịch chiếu theo ngày
        $schedulesByDate = [];
        foreach ($schedules as $schedule) {
            $date = $schedule['date'];
            if (!isset($schedulesByDate[$date])) {
                $schedulesByDate[$date] = [];
            }
            $schedulesByDate[$date][] = $schedule;
        }

        // Hiển thị view
        include 'views/layouts/header.php';
        include 'views/schedules/play_schedules.php';
        include 'views/layouts/footer.php';
    }

    /**
     * Tìm kiếm lịch chiếu
     
    public function search()
    {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        if (empty($query) && $date == date('Y-m-d')) {
            header('Location: index.php?route=schedule');
            exit;
        }

        $schedules = $this->scheduleModel->searchSchedules($query, $date);

        // Hiển thị view
        include 'views/layouts/header.php';
        include 'views/schedules/search_results.php';
        include 'views/layouts/footer.php';
    }
        */
}
