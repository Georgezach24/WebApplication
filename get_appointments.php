<?php
require_once 'db_config.php'; // Σύνδεση με τη βάση δεδομένων

if (isset($_POST['patient_email'])) {
    $patient_email = $_POST['patient_email'];

    // SQL ερώτηση για ανάκτηση ραντεβού συγκεκριμένου ασθενούς μέσω του πίνακα books
    $stmt_appointments = $conn->prepare("
        SELECT r.a_date, r.a_time, r.a_desc, r.a_state, d.D_Name AS doctor_first_name, d.D_Surname AS doctor_last_name
        FROM rantevou r
        JOIN books b ON r.id_appointment = b.id_appointment
        JOIN asthenis a ON b.AT = a.AT
        JOIN xristis x ON a.AT = x.AT
        JOIN iatros d ON r.a_doc = d.D_id
        WHERE x.Email = ?
    ");
    
    $stmt_appointments->bind_param("s", $patient_email);
    $stmt_appointments->execute();
    $result_appointments = $stmt_appointments->get_result();
    
    $appointments = [];
    while ($row = $result_appointments->fetch_assoc()) {
        $appointments[] = [
            'a_date' => $row['a_date'],
            'a_time' => $row['a_time'],
            'a_desc' => $row['a_desc'],
            'a_state' => $row['a_state'],
            'a_doc' => $row['doctor_first_name'] . ' ' . $row['doctor_last_name']
        ];
    }

    $stmt_appointments->close();
    $conn->close();

    // Επιστροφή αποτελεσμάτων ως JSON
    echo json_encode($appointments);
}
?>
