<?php
session_start();
require_once 'db_config.php';

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

$patientAT = $_GET['at'] ?? ''; // Retrieve patient's AT from the query string

if (empty($patientAT)) {
    header('Location: doctor_dashboard.php');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch patient medical history entries from `eggrafi` table
$stmt_history = $conn->prepare("SELECT id_entry, e_date, e_healthproblems, e_cure FROM eggrafi WHERE patient_AT = ?");
$stmt_history->bind_param("s", $patientAT);
$stmt_history->execute();
$stmt_history->bind_result($id_entry, $date, $healthProblems, $cure);
$history = [];
while ($stmt_history->fetch()) {
    $history[] = ['id_entry' => $id_entry, 'date' => $date, 'healthProblems' => $healthProblems, 'cure' => $cure];
}
$stmt_history->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ιατρικό Ιστορικό Ασθενή</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Ιατρικό Ιστορικό Ασθενή</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ημερομηνία</th>
                    <th>Προβλήματα Υγείας</th>
                    <th>Θεραπεία</th>
                    <th>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $entry): ?>
                    <tr>
                        <td><?php echo $entry['date']; ?></td>
                        <td><?php echo $entry['healthProblems']; ?></td>
                        <td><?php echo $entry['cure']; ?></td>
                        <td>
                            <a href='edit_history.php?id=<?php echo $entry['id_entry']; ?>' class='btn'>Επεξεργασία</a>
                            <a href='export_history_pdf.php?id=<?php echo $entry['id_entry']; ?>' class='btn'>Εξαγωγή σε PDF</a>
                            <a href='export_history_excel.php?id=<?php echo $entry['id_entry']; ?>' class='btn'>Εξαγωγή σε Excel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Δεν υπάρχουν εγγραφές στο ιατρικό ιστορικό.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
