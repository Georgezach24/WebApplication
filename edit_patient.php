<?php
session_start();
require_once 'db_config.php';

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor' && $_SESSION['role'] !== 'Patient') {
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

// Fetch patient details from `asthenis` table
$stmt_asthenis = $conn->prepare("SELECT P_Name, P_Surname, AT, P_AMKA FROM asthenis WHERE AT = ?");
$stmt_asthenis->bind_param("s", $at);
$stmt_asthenis->execute();
$stmt_asthenis->bind_result($firstName, $lastName, $at, $amka);
$stmt_asthenis->fetch();
$stmt_asthenis->close();

// Update patient details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFirstName = $_POST['FirstName'];
    $newLastName = $_POST['LastName'];
    $newAT = $_POST['AT'];
    $newAMKA = $_POST['AMKA'];

    // Update `asthenis` table (FirstName, LastName, AT, AMKA)
    $stmt_update_asthenis = $conn->prepare("UPDATE asthenis SET P_Name = ?, P_Surname = ?, AT = ?, P_AMKA = ? WHERE AT = ?");
    $stmt_update_asthenis->bind_param("sssss", $newFirstName, $newLastName, $newAT, $newAMKA, $at);

    // Execute the update and check for errors
    if ($stmt_update_asthenis->execute()) {
        echo "Τα στοιχεία του ασθενή ενημερώθηκαν με επιτυχία!";
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        echo "Σφάλμα κατά την ενημέρωση: " . $stmt_update_asthenis->error;
    }

    $stmt_update_asthenis->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Επεξεργασία Ασθενή</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Επεξεργασία Στοιχείων Ασθενή</h2>
        <form method="post">
            <div class="form-group">
                <label for="FirstName">Όνομα:</label>
                <input type="text" class="form-control" name="FirstName" value="<?php echo $firstName; ?>" required>
            </div>
            <div class="form-group">
                <label for="LastName">Επώνυμο:</label>
                <input type="text" class="form-control" name="LastName" value="<?php echo $lastName; ?>" required>
            </div>
            <div class="form-group">
                <label for="AT">ΑΤ:</label>
                <input type="text" class="form-control" name="AT" value="<?php echo $at; ?>" required>
            </div>
            <div class="form-group">
                <label for="AMKA">AMKA:</label>
                <input type="text" class="form-control" name="AMKA" value="<?php echo $amka; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Αποθήκευση Αλλαγών</button>
        </form>
    </div>
</body>
</html>
