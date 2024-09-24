<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in as either a Patient or Doctor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Patient' && $_SESSION['role'] !== 'Doctor')) {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];
$role = $_SESSION['role'];
$patientAT = '';
$appointmentID = $_GET['id'] ?? ''; // Retrieve appointment ID from query string

if (empty($appointmentID)) {
    header('Location: patient_dashboard.php');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the user is a patient, verify that the appointment belongs to them
if ($role === 'Patient') {
    // Fetch patient AT to ensure the appointment belongs to the patient
    $stmt = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($patientAT);
    $stmt->fetch();
    $stmt->close();

    // Check if the appointment belongs to the patient by checking the 'books' table
    $stmt_check = $conn->prepare("
        SELECT b.AT 
        FROM books b
        JOIN rantevou r ON b.id_appointment = r.id_appointment
        WHERE b.AT = ? AND r.id_appointment = ?");
    $stmt_check->bind_param("ss", $patientAT, $appointmentID);
    $stmt_check->execute();
    $stmt_check->store_result();

    // If the appointment does not belong to the patient, show unauthorized message
    if ($stmt_check->num_rows === 0) {
        echo "Unauthorized action.";
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();
}

// If the user is a doctor, they can delete any appointment without ownership checks

// Delete the appointment
$stmt_delete = $conn->prepare("DELETE FROM rantevou WHERE id_appointment = ?");
$stmt_delete->bind_param("s", $appointmentID);

if ($stmt_delete->execute()) {
    if ($role === 'Patient') {
        header('Location: patient_dashboard.php');
    } elseif ($role === 'Doctor') {
        header('Location: doctor_dashboard.php');
    }
} else {
    echo "Error deleting appointment: " . $stmt_delete->error;
}

$stmt_delete->close();
$conn->close();
?>
