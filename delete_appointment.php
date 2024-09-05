<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];
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

// Fetch patient AT to ensure the appointment belongs to the patient
$stmt = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($patientAT);
$stmt->fetch();
$stmt->close();

// Check if the appointment belongs to the patient
$stmt_check = $conn->prepare("SELECT a_doc FROM rantevou WHERE id_appointment = ?");
$stmt_check->bind_param("s", $appointmentID);
$stmt_check->execute();
$stmt_check->bind_result($appointmentDoc);
$stmt_check->fetch();
$stmt_check->close();

if ($appointmentDoc !== $patientAT) {
    echo "Unauthorized action.";
    exit();
}

// Delete the appointment
$stmt_delete = $conn->prepare("DELETE FROM rantevou WHERE id_appointment = ?");
$stmt_delete->bind_param("s", $appointmentID);

if ($stmt_delete->execute()) {
    header('Location: patient_dashboard.php');
} else {
    echo "Error deleting appointment: " . $stmt_delete->error;
}

$stmt_delete->close();
$conn->close();
?>
