<?php
session_start();
require_once 'db_config.php';

// Έλεγχος αν ο χρήστης έχει το ρόλο της γραμματείας
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Secretary') {
    header('Location: login.html');
    exit();
}

// Σύνδεση με τη βάση δεδομένων
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Χειρισμός της εγγραφής νέου ασθενούς
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_patient'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $amka = $_POST['amka'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Κρυπτογράφηση του κωδικού

    // Έλεγχος αν υπάρχει ήδη ο χρήστης με το ίδιο ΑΜΚΑ ή email
    $check_sql = "SELECT * FROM xristis WHERE AT = ? OR Email = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param('ss', $amka, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Υπάρχει ήδη ασθενής με το ίδιο ΑΜΚΑ ή email, ανακατεύθυνση με μήνυμα λάθους
        $_SESSION['error'] = "Υπάρχει ήδη ασθενής με αυτό το ΑΜΚΑ ή email.";
        header('Location: secretary_dashboard.php');
    } else {
        // Εισαγωγή νέου ασθενούς
        $sql = "INSERT INTO xristis (AT, FirstName, LastName, Email, Password, Role) VALUES (?, ?, ?, ?, ?, 'Patient')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $amka, $first_name, $last_name, $email, $password);

        if ($stmt->execute()) {
            // Επιτυχής εγγραφή, ανακατεύθυνση με μήνυμα επιτυχίας
            $_SESSION['success'] = "Ο ασθενής εγγράφηκε επιτυχώς!";
            header('Location: secretary_dashboard.php');
        } else {
            // Σφάλμα κατά την εγγραφή
            $_SESSION['error'] = "Σφάλμα κατά την εγγραφή: " . $conn->error;
            header('Location: secretary_dashboard.php');
        }
        $stmt->close();
    }

    $stmt_check->close();
}

// Κλείσιμο της σύνδεσης με τη βάση δεδομένων
$conn->close();
?>
