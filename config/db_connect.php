<?php
// Database connection configuration
$servername = "127.0.0.1";
$username = "root"; // Default WAMP username
$password = ""; // Default WAMP password (empty)
$dbname = "hospital_db";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for better Unicode support
$conn->set_charset("utf8mb4");

// You can now use $conn to perform database operations
?>
