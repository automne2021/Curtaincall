<?php
// Database connection
require_once 'config/database.php';

// Check if the admins table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'admins'")->num_rows > 0;

if (!$tableExists) {
    // Create admins table
    $createTable = "CREATE TABLE `admins` (
        `admin_id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(30) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(50) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`admin_id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $conn->query($createTable);
    
    echo "Admin table created successfully.<br>";
} else {
    echo "Admin table already exists.<br>";
}

// Check if admin user exists
$adminExists = $conn->query("SELECT * FROM `admins` WHERE `username` = 'admin'")->num_rows > 0;

if (!$adminExists) {
    // Create admin user
    // Using a pre-generated hash for 'admin123'
    $password = '$2y$10$Tn/0xi2s.OElsEgYTieQX.mcyzYyuBcGYHB8f3eQKkaBH8JRvhh8e';
    $email = 'admin@curtaincall.com';
    
    $insertAdmin = "INSERT INTO `admins` (`username`, `password`, `email`) VALUES ('admin', ?, ?)";
    $stmt = $conn->prepare($insertAdmin);
    $stmt->bind_param("ss", $password, $email);
    $result = $stmt->execute();
    
    if ($result) {
        echo "Admin user created successfully.<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating admin user: " . $stmt->error . "<br>";
    }
} else {
    echo "Admin user already exists.<br>";
}

echo "<br><a href='admin.php'>Go to Admin Login</a>";