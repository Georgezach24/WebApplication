<?php
session_start();
require_once 'db_config.php';

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

$id_entry = $_GET['id'] ?? ''; // Retrieve medical history ID from the query string

if (empty($id_entry)) {
    header('Location: doctor_dashboard.php');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch medical history details from `eggrafi` table
$stmt_history = $conn->prepare("SELECT e_healthproblems, e_cure FROM eggrafi WHERE id_entry = ?");
$stmt_history->bind_param("s", $id_entry);
$stmt_history->execute();
$stmt_history->bind_result($healthProblems, $cure);
$stmt_history->fetch();
$stmt_history->close();

// Update medical history details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newHealthProblems = $_POST['HealthProblems'];
    $newCure = $_POST['Cure'];

    // Update the `eggrafi` table
    $stmt_update_history = $conn->prepare("UPDATE eggrafi SET e_healthproblems = ?, e_cure = ? WHERE id_entry = ?");
    $stmt_update_history->bind_param("sss", $newHealthProblems, $newCure, $id_entry);

    if ($stmt_update_history->execute()) {
        echo "Το ιατρικό ιστορικό ενημερώθηκε με επιτυχία!";
        header("Location: view_history.php?at=" . $patientAT);
        exit();
    } else {
        echo "Σφάλμα κατά την ενημέρωση: " . $stmt_update_history->error;
    }

    $stmt_update_history->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Επεξεργασία Ιατρικής Εγγραφής</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Επεξεργασία Ιατρικής Εγγραφής</h2>
        <form method="post">
            <div class="form-group">
                <label for="HealthProblems">Προβλήματα Υγείας:</label>
                <textarea class="form-control" name="HealthProblems" required><?php echo $healthProblems; ?></textarea>
            </div>
            <div class="form-group">
                <label for="Cure">Θεραπεία:</label>
                <textarea class="form-control" name="Cure" required><?php echo $cure; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Αποθήκευση Αλλαγών</button>
        </form>
    </div>
</body>
</html>
