<?php
session_start();
require_once 'db_config.php';

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor' && $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

// Get appointment ID from URL
$id_appointment = $_GET['id'];

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointment details
$result = $conn->query("SELECT * FROM rantevou WHERE id_appointment = '$id_appointment'");
$appointment = $result->fetch_assoc();

// If form is submitted, update the appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a_date = $_POST['a_date'];
    $a_time = $_POST['a_time'];
    $a_desc = $_POST['a_desc'];
    $a_state = $_POST['a_state'];  // Status can be Updated/Cancelled/Completed
    
    $update_sql = "UPDATE rantevou SET a_date='$a_date', a_time='$a_time', a_desc='$a_desc', a_state='$a_state' WHERE id_appointment='$id_appointment'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "Το ραντεβού ενημερώθηκε με επιτυχία!";
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        echo "Σφάλμα κατά την ενημέρωση: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Επεξεργασία Ραντεβού</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Επεξεργασία Ραντεβού</h2>
        <form method="post">
            <div class="form-group">
                <label for="a_date">Ημερομηνία:</label>
                <input type="date" class="form-control" name="a_date" value="<?php echo $appointment['a_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="a_time">Ώρα:</label>
                <input type="time" class="form-control" name="a_time" value="<?php echo $appointment['a_time']; ?>" required>
            </div>
            <div class="form-group">
                <label for="a_desc">Περιγραφή:</label>
                <textarea class="form-control" name="a_desc" required><?php echo $appointment['a_desc']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="a_state">Κατάσταση:</label>
                <select class="form-control" name="a_state" required>
                    <option value="Δημιουργημένο" <?php if ($appointment['a_state'] == 'Δημιουργημένο') echo 'selected'; ?>>Δημιουργημένο</option>
                    <option value="Ολοκληρωμένο" <?php if ($appointment['a_state'] == 'Ολοκληρωμένο') echo 'selected'; ?>>Ολοκληρωμένο</option>
                    <option value="Ακυρωμένο" <?php if ($appointment['a_state'] == 'Ακυρωμένο') echo 'selected'; ?>>Ακυρωμένο</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Αποθήκευση Αλλαγών</button>
        </form>
    </div>
</body>
</html>
