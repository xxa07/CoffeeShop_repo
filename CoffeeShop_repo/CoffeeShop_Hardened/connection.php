<?php
$host = "localhost";
$username = "root"; // Default XAMPP user
$password = ""; // XAMPP default password is empty
$database = "coffeeshop_db"; // Database name

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check if connection is successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8 for proper text encoding
$conn->set_charset("utf8mb4");

?>