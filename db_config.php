<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Replace with your actual database username
define('DB_PASSWORD', ''); // Replace with your actual database password
define('DB_NAME', 'healthclinic_db'); // Your database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
