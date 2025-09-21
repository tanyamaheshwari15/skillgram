<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = "localhost";
$user = "root";
$password = ""; // your MySQL password
$dbname = "skillgram_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
