<?php
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    // Redirect to login page if the user is not logged in or not a doctor
    header('Location: login.html');
    exit();
}

// If the user is a Doctor, display a welcome message
echo "Welcome, Dr. " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "!";
?>

<!-- Logout Button -->
<a href="logout.php" class="btn btn-primary">Logout</a>
