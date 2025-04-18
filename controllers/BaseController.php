<?php
require_once 'models/Theater.php';

class BaseController {
    protected $conn;
    protected $theaterModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->theaterModel = new Theater($conn);
    }
    
    protected function getTheatersForNav() {
        return $this->theaterModel->getAllTheaters();
    }
    
    protected function loadCommonData() {
        $GLOBALS['theaters_result'] = $this->getTheatersForNav();
    }
}