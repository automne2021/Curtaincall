<?php
require_once 'models/Play.php';
require_once 'models/Theater.php';

class SearchController {
    private $conn;
    private $playModel;
    private $theaterModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
    }

    public function index() {
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $theaters_result = $this->theaterModel->getAllTheaters();
        
        // Initial search results
        $results = [];
        if (!empty($query)) {
            $results = $this->playModel->searchPlays($query);
        }
        
        include 'views/layouts/header.php';
        include 'views/search/index.php';
        include 'views/layouts/footer.php';
    }
    
    public function ajaxSearch() {
        header('Content-Type: application/json');
        
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $results = [];
        
        if (strlen($query) >= 2) {
            $results = $this->playModel->searchPlays($query, 5); // Limit to 5 results for dropdown
        }
        
        echo json_encode([
            'success' => true,
            'results' => $results
        ]);
    }
    
    public function ajaxSearchFull() {
        header('Content-Type: application/json');
        
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $results = [];
        
        if (!empty($query)) {
            $results = $this->playModel->searchPlays($query);
        }
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'html' => $this->renderSearchResults($results)
        ]);
    }
    
    private function renderSearchResults($results) {
        ob_start();
        if (!empty($results)) {
            foreach ($results as $play) {
                ?>
                <div class="col-md-3 mb-4">
                    <a href="index.php?route=play/view&play_id=<?= $play['play_id'] ?>" class="play-card-link">
                        <div class="play-card">
                            <img src="<?= $play['image'] ?>" class="card-img-top" alt="<?= $play['title'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= '[' . $play['theater_name'] . '] ' . $play['title'] ?></h5>
                                <p class="fw-bold mb-1">Từ <?= number_format($play['min_price'], 0, ',', '.') ?>đ</p>
                                <p class="date-info"><i class="bi bi-calendar-event me-2"></i>
                                    <?php if (!empty($play['date'])): ?>
                                        <?= date("d \\t\h\á\\n\g m, Y", strtotime($play['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Không tìm thấy vở diễn nào phù hợp với yêu cầu tìm kiếm.
                </div>
            </div>
            <?php
        }
        return ob_get_clean();
    }
}