<?php
session_start();
require_once 'db_config.php';

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

$at = $_GET['id'] ?? ''; // Retrieve patient's AT from the query string

if (empty($at)) {
    header('Location: doctor_dashboard.php');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete patient from `asthenis` table
$stmt_delete_asthenis = $conn->prepare("DELETE FROM asthenis WHERE AT = ?");
$stmt_delete_asthenis->bind_param("s", $at);
$stmt_delete_asthenis->execute();
$stmt_delete_asthenis->close();

// Delete patient from `xristis` table
$stmt_delete_xristis = $conn->prepare("DELETE FROM xristis WHERE AT = ?");
$stmt_delete_xristis->bind_param("s", $at);
$stmt_delete_xristis->execute();
$stmt_delete_xristis->close();

$conn->close();

// Redirect back to the doctor dashboard after deletion
header('Location: doctor_dashboard.php');
exit();
?>
