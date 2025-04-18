<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Adjust for Vietnam time zone
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
