<?php
require_once 'models/Play.php';
require_once 'models/Theater.php';

class HomeController
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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $plays_per_page = 8;

        // Set default sorting
        $sort_field = 'date';
        $sort_dir = 'desc';
        $theater_id = '';

        $result = $this->playModel->getUpComingPlays($plays_per_page, $page);
        $hot_plays = $this->playModel->getHotPlays(6);
        $theaters_result = $this->theaterModel->getAllTheaters();

        $total_plays = $this->playModel->getTotalPlays();
        $total_pages = ceil($total_plays / $plays_per_page);

        $GLOBALS['sort_field'] = $sort_field;
        $GLOBALS['sort_dir'] = $sort_dir;
        $GLOBALS['theater_id'] = $theater_id;

        extract([
            'theater_id' => $theater_id,
            'sort_field' => $sort_field,
            'sort_dir' => $sort_dir,
            'page' => $page,
            'total_pages' => $total_pages,
            'hot_plays' => $hot_plays
        ]);

        // Load the views
        include 'views/layouts/header.php';
        include 'views/home/index.php';
        include 'views/layouts/footer.php';
    }
}
