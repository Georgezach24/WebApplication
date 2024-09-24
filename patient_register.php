<?php
require_once 'db_config.php'; // Σύνδεση με τη βάση δεδομένων

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $at = $_POST['at']; // Αριθμός Ταυτότητας
    $firstName = $_POST['firstName']; // Όνομα
    $lastName = $_POST['lastName']; // Επώνυμο
    $email = $_POST['email']; // Email
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Κρυπτογραφημένος κωδικός
    $amka = isset($_POST['amka']) ? $_POST['amka'] : null; // ΑΜΚΑ για Ασθενείς
    $role = 'Patient'; // Σταθερή τιμή για τον ρόλο του ασθενούς

    // Έλεγχος για υπάρχον χρήστη με το ίδιο ΑΤ ή Email
    $stmt = $conn->prepare("SELECT * FROM xristis WHERE AT = ? OR Email = ?");
    $stmt->bind_param("ss", $at, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: Ο χρήστης υπάρχει ήδη.";
    } else {
        // Εισαγωγή νέου χρήστη ως ασθενής
        $sql = "INSERT INTO xristis (AT, FirstName, LastName, Email, Password, Role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $at, $firstName, $lastName, $email, $password, $role);
        
        if ($stmt->execute()) {
            // Εισαγωγή στον πίνακα `asthenis`
            $sql_patient = "INSERT INTO asthenis (P_AMKA, AT, P_Name, P_Surname, P_DateOfEntry) VALUES (?, ?, ?, ?, NOW())";
            $stmt_patient = $conn->prepare($sql_patient);
            $stmt_patient->bind_param("ssss", $amka, $at, $firstName, $lastName);
            if ($stmt_patient->execute()) {
                echo "Ο ασθενής εγγράφηκε επιτυχώς!" . $stmt_patient->error;
                header("Location: success_register.html");
            } else {
                echo "Σφάλμα κατά την εισαγωγή στον πίνακα ασθενών: " . $stmt_patient->error;
            }
            $stmt_patient->close();
        } else {
            echo "Σφάλμα κατά την εγγραφή: " . $stmt->error;
        }
    }
    $stmt->close();
}
$conn->close();
?>
