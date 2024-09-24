<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Doctor (super user)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Επιλογή του ρόλου
    $at = $_POST['at']; // Αριθμός Ταυτότητας (unique identifier)

    // Κρυπτογράφηση του κωδικού με bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Ξεκινάμε με την εγγραφή στον πίνακα xristis
    $stmt = $conn->prepare("INSERT INTO xristis (AT, FirstName, LastName, Email, Password, Role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $at, $firstName, $lastName, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Ο χρήστης προστέθηκε επιτυχώς!<br>";

        // Αν ο ρόλος είναι "Γιατρός", εισάγουμε στον πίνακα iatros χωρίς D_id, γιατί είναι AUTO_INCREMENT
        if ($role === 'Doctor') {
            $specialty = $_POST['specialty'];
            $stmt_specialty = $conn->prepare("INSERT INTO iatros (D_Name, D_Surname, Specialty) VALUES (?, ?, ?)");
            $stmt_specialty->bind_param("sss", $firstName, $lastName, $specialty);

            if ($stmt_specialty->execute()) {
                echo "Ο γιατρός προστέθηκε με ειδικότητα επιτυχώς!";
            } else {
                echo "Σφάλμα κατά την εισαγωγή του γιατρού: " . $stmt_specialty->error;
            }
            $stmt_specialty->close();
        }
        // Αν ο ρόλος είναι "Ασθενής", μπορούμε να προσθέσουμε σχετική λογική εδώ αν υπάρχει πίνακας "asthenis"
        else if ($role === 'Patient') {
            // Συμπληρώνεις με επιπλέον λογική αν χρειάζεται για ασθενή
        }
        // Αν ο ρόλος είναι "Γραμματέας", μπορούμε να προσθέσουμε σχετική λογική εδώ αν υπάρχει πίνακας "secretary"
        else if ($role === 'Secretary') {
            // Συμπληρώνεις με επιπλέον λογική αν χρειάζεται για γραμματέα
        }

    } else {
        echo "Σφάλμα κατά την εισαγωγή: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Εγγραφή Νέου Χρήστη</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script>
        function toggleSpecialtyField() {
            var role = document.getElementById("role").value;
            var specialtyField = document.getElementById("specialty-field");
            if (role === "Doctor") {
                specialtyField.style.display = "block"; // Εμφάνιση πεδίου ειδικότητας
            } else {
                specialtyField.style.display = "none"; // Απόκρυψη πεδίου ειδικότητας
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Εγγραφή Νέου Χρήστη</h2>
        <form method="post">
            <div class="form-group">
                <label for="first_name">Όνομα:</label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Επώνυμο:</label>
                <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Κωδικός:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Ρόλος Χρήστη:</label>
                <select class="form-control" name="role" id="role" onchange="toggleSpecialtyField()" required>
                    <option value="Patient">Ασθενής</option>
                    <option value="Secretary">Γραμματέας</option>
                    <option value="Doctor">Γιατρός</option>
                </select>
            </div>
            <!-- Πεδίο για την ειδικότητα (εμφανίζεται μόνο αν ο ρόλος είναι 'Γιατρός') -->
            <div class="form-group" id="specialty-field" style="display: none;">
                <label for="specialty">Ειδικότητα:</label>
                <input type="text" class="form-control" name="specialty">
            </div>
            <div class="form-group">
                <label for="at">Αριθμός Ταυτότητας (AT):</label>
                <input type="text" class="form-control" name="at" required>
            </div>
            <button type="submit" class="btn btn-primary">Εγγραφή Χρήστη</button>
        </form>
    </div>
</body>
</html>
