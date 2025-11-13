<?php
// Database credentials for XAMPP setup
$servername = "localhost";
$username = "root";   // Default XAMPP MySQL username
$password = "";       // Default XAMPP MySQL password (usually blank)
$dbname = "btb_data"; // The database name you created earlier (create database btb_data)

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Stop script execution and output a server-side error if connection fails
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for better character support
$conn->set_charset("utf8mb4");

// NOTE: This file only defines the $conn variable. It should not output
// any HTML or text, which would break your JSON APIs.
?>