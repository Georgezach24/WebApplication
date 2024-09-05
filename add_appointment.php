<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthclinic_db";

// Σύνδεση με βάση δεδομένων
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$amka = $_POST['amka'];
$doctor_id = $_POST['doctor']; // Assuming this will be a doctor ID
$appointment_date = $_POST['date'];
$appointment_time = $_POST['time'];
$appointment_desc = $_POST['description'];

$sql_patient = "SELECT AT FROM asthenis WHERE P_AMKA = '$amka'";
$result_patient = $conn->query($sql_patient);

if ($result_patient->num_rows > 0) {
    $row = $result_patient->fetch_assoc();
    $patient_id = $row['AT'];

    // Insert the appointment
    $sql_insert = "INSERT INTO rantevou (id_appointment, a_date, a_time, a_desc, a_doc, a_state)
                   VALUES (UUID(), '$appointment_date', '$appointment_time', '$appointment_desc', '$doctor_id', 'scheduled')";

    if ($conn->query($sql_insert) === TRUE) {
        header("Location: success.php");
        exit();
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
} else {
    echo "No patient found with the given AMKA!";
}


$conn->close();
?>
