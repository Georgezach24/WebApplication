<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinic_db";

// Σύνδεση με βάση δεδομένων
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$amka = $_POST['amka'];
$doctor = $_POST['doctor'];
$date = $_POST['date'];

// Εισαγωγή ραντεβού
$sql = "INSERT INTO Appointments (patient_id, doctor_id, appointment_date) VALUES ((SELECT id FROM Patients WHERE amka='$amka'), (SELECT id FROM Users WHERE first_name='$doctor'), '$date')";

if ($conn->query($sql) === TRUE) {
    echo "Το ραντεβού καταχωρήθηκε επιτυχώς!";
} else {
    echo "Σφάλμα: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
