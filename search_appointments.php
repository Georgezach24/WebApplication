<?php
session_start();
require_once 'db_config.php'; 

// Δημιουργία σύνδεσης
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// Ανάκτηση των κριτηρίων από τη φόρμα
$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : null;
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : null;
$surname = isset($_POST['surname']) ? $_POST['surname'] : null;
$amka = isset($_POST['amka']) ? $_POST['amka'] : null;
$state = isset($_POST['state']) ? $_POST['state'] : 'Pending';  // Προκαθορισμένη τιμή για εκκρεμή ραντεβού

// Προετοιμασία SQL με βάση τα φίλτρα
$sql = "SELECT r.a_date, r.a_time, r.a_desc, r.a_state, x.FirstName, x.LastName, a.P_AMKA 
        FROM rantevou r
        JOIN books b ON r.id_appointment = b.id_appointment
        JOIN asthenis a ON b.AT = a.AT
        JOIN xristis x ON a.AT = x.AT
        WHERE 1=1"; // Βάση για δυναμικό ερώτημα

$params = [];

// Προσθήκη φίλτρων στο SQL ερώτημα
if (!empty($date_from)) {
    $sql .= " AND r.a_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $sql .= " AND r.a_date <= ?";
    $params[] = $date_to;
}

if (!empty($surname)) {
    $sql .= " AND x.LastName LIKE ?";
    $params[] = '%' . $surname . '%';  // Χρησιμοποιούμε LIKE για μερική αναζήτηση
}

if (!empty($amka)) {
    $sql .= " AND a.P_AMKA = ?";
    $params[] = $amka;
}

if (!empty($state)) {
    $sql .= " AND r.a_state = ?";
    $params[] = $state;
}

// Αν δεν δοθεί κανένα φίλτρο, εμφάνιση εκκρεμών ραντεβού της ημέρας
if (empty($date_from) && empty($date_to) && empty($surname) && empty($amka) && empty($state)) {
    $current_date = date('Y-m-d');
    $sql .= " AND r.a_state = 'Pending' AND r.a_date = ?";
    $params[] = $current_date;
}

// Εκτέλεση του ερωτήματος
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);  // Προετοιμασία παραμέτρων
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αποτελέσματα Αναζήτησης Ραντεβού</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Αποτελέσματα Αναζήτησης Ραντεβού</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ημερομηνία</th>
                <th>Ώρα</th>
                <th>Περιγραφή</th>
                <th>Κατάσταση</th>
                <th>Όνομα Ασθενούς</th>
                <th>Επώνυμο Ασθενούς</th>
                <th>ΑΜΚΑ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['a_date']}</td>
                            <td>{$row['a_time']}</td>
                            <td>{$row['a_desc']}</td>
                            <td>{$row['a_state']}</td>
                            <td>{$row['FirstName']}</td>
                            <td>{$row['LastName']}</td>
                            <td>{$row['P_AMKA']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Δεν βρέθηκαν ραντεβού</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="javascript:history.back()" class="btn btn-primary">Επιστροφή</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
