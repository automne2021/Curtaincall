<?php
require_once 'models/Theater.php';
require_once 'models/Play.php';
require_once 'models/Seat.php';

class TheaterController
{
    private $conn;
    private $theaterModel;
    private $playModel;

    public function index()
    {
        $this->theaters();
    }

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->theaterModel = new Theater($conn);
        $this->playModel = new Play($conn);
    }

    // Helper method to check if admin is logged in
    private function checkAdminAuth()
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=admin/login');
            exit;
        }
    }

    // List all theaters
    public function theaters()
    {
        $this->checkAdminAuth();

        // Get current page from query string, default to 1 if not set
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 10;

        // Get paginated theaters
        $result = $this->theaterModel->getPaginatedTheaters($page, $per_page);
        $theaters = $result['theaters'];
        $pagination = $result['pagination'];

        // Set base URL for pagination
        $base_url = BASE_URL . 'index.php?route=admin/theaters';

        include 'views/admin/layouts/header.php';
        include 'views/admin/theaters/theaters.php';
        include 'views/admin/layouts/footer.php';
    }

    // Create new theater
    public function createTheater()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = [];

            if (empty($_POST['theater_id'])) {
                $errors['theater_id'] = 'ID nhà hát là bắt buộc';
            } else {
                // Check if theater_id follows the pattern (3 letters)
                if (!preg_match('/^[A-Z]{3}$/', $_POST['theater_id'])) {
                    $errors['theater_id'] = 'ID nhà hát phải là 3 chữ cái viết hoa (A-Z). Ví dụ: IDE';
                }

                // Check if theater_id already exists
                $existingTheater = $this->theaterModel->getTheaterById($_POST['theater_id']);
                if ($existingTheater) {
                    $errors['theater_id'] = 'ID nhà hát đã tồn tại. Vui lòng chọn ID khác.';
                }
            }

            if (empty($_POST['name'])) {
                $errors['name'] = 'Tên nhà hát là bắt buộc';
            }

            if (empty($_POST['location'])) {
                $errors['location'] = 'Địa chỉ là bắt buộc';
            }

            if (empty($errors)) {
                $theater_data = [
                    'theater_id' => $_POST['theater_id'],
                    'name' => $_POST['name'],
                    'location' => $_POST['location']
                ];

                $success = $this->theaterModel->createTheater($theater_data);

                if ($success) {
                    $_SESSION['success_message'] = 'Tạo nhà hát thành công!';
                    header('Location: index.php?route=admin/theaters');
                    exit;
                } else {
                    $_SESSION['error_message'] = 'Lỗi khi tạo nhà hát';
                }
            } else {
                $_SESSION['error_message'] = 'Vui lòng sửa các lỗi trong biểu mẫu';
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
            }
        }

        include 'views/admin/layouts/header.php';
        include 'views/admin/theaters/createTheater.php';
        include 'views/admin/layouts/footer.php';
    }

    public function editTheater()
    {
        $this->checkAdminAuth();

        $theater_id = $_GET['id'] ?? null;
        if (!$theater_id) {
            $_SESSION['error_message'] = 'Không có ID nhà hát nào được chỉ định';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        $theater = $this->theaterModel->getTheaterById($theater_id);
        if (!$theater) {
            $_SESSION['error_message'] = 'Nhà hát không tồn tại';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = [];

            if (empty($_POST['name'])) {
                $errors['name'] = 'Tên nhà hát là bắt buộc';
            }

            if (empty($_POST['location'])) {
                $errors['location'] = 'Địa chỉ là bắt buộc';
            }

            if (empty($errors)) {
                $theater_data = [
                    'theater_id' => $theater_id,
                    'name' => $_POST['name'],
                    'location' => $_POST['location']
                ];

                $success = $this->theaterModel->updateTheater($theater_data);

                if ($success) {
                    $_SESSION['success_message'] = 'Cập nhật nhà hát thành công!';
                    header('Location: index.php?route=admin/theaters');
                    exit;
                } else {
                    $_SESSION['error_message'] = 'Lỗi khi cập nhật nhà hát';
                }
            } else {
                $_SESSION['error_message'] = 'Vui lòng sửa các lỗi trong biểu mẫu';
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
            }
        }

        include 'views/admin/layouts/header.php';
        include 'views/admin/theaters/editTheater.php';
        include 'views/admin/layouts/footer.php';
    }

    // Delete theater
    public function deleteTheater()
    {
        $this->checkAdminAuth();

        $theater_id = $_GET['id'] ?? null;
        if (!$theater_id) {
            $_SESSION['error_message'] = 'Không có ID nhà hát nào được chỉ định';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        $theater = $this->theaterModel->getTheaterById($theater_id);
        if (!$theater) {
            $_SESSION['error_message'] = 'Nhà hát không tồn tại';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        // Check if theater has associated plays
        $hasPlays = $this->theaterModel->hasPlays($theater_id);

        if ($hasPlays) {
            $_SESSION['error_message'] = 'Không thể xóa nhà hát vì nó có các vở kịch liên quan. Vui lòng xóa các vở kịch trước.';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        $success = $this->theaterModel->deleteTheater($theater_id);

        if ($success) {
            $_SESSION['success_message'] = 'Xóa nhà hát thành công!';
        } else {
            $_SESSION['error_message'] = 'Lỗi khi xóa nhà hát';
        }

        header('Location: index.php?route=admin/theaters');
        exit;
    }

    public function viewTheater()
    {
        $this->checkAdminAuth();

        $theater_id = $_GET['id'] ?? null;
        if (!$theater_id) {
            $_SESSION['error_message'] = 'Không có ID nhà hát nào được chỉ định';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        $theater = $this->theaterModel->getTheaterById($theater_id);
        if (!$theater) {
            $_SESSION['error_message'] = 'Nhà hát không tồn tại';
            header('Location: index.php?route=admin/theaters');
            exit;
        }

        // Get seat map for this theater
        $seatModel = new Seat($this->conn);
        $seatMap = $seatModel->getSeatMapByTheater($theater_id);

        // Get seat prices by type
        $seatPrices = $seatModel->getSeatPrices($theater_id);

        // Create a mapping of seat types to prices for display
        $seatTypes = [];
        foreach ($seatPrices as $type => $price) {
            $seatTypes[$type] = $price;
        }

        include 'views/admin/layouts/header.php';
        include 'views/admin/theaters/viewTheater.php';
        include 'views/admin/layouts/footer.php';
    }
}
