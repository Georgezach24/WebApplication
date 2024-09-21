<?php
session_start();
require_once 'db_config.php'; // Σύνδεση με τη βάση δεδομένων

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος και έχει το ρόλο της γραμματείας
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Secretary') {
    header('Location: login.html');
    exit();
}

// Ανάκτηση λίστας ασθενών
$patients = $conn->query("SELECT x.Email, x.FirstName, x.LastName, x.AT, a.P_AMKA FROM xristis x LEFT JOIN asthenis a ON x.AT = a.AT WHERE x.Role = 'Patient'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Λήψη των δεδομένων από τη φόρμα
    $firstName = $_POST['first_name']; // Όνομα
    $lastName = $_POST['last_name']; // Επώνυμο
    $email = $_POST['email']; // Email
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Κρυπτογραφημένος κωδικός
    $amka = $_POST['amka']; // ΑΜΚΑ
    $at = $_POST['at']; // Αριθμός Ταυτότητας (AT)
    $role = 'Patient'; // Ρόλος, προεπιλογή: Patient

    // Έλεγχος για υπάρχον χρήστη με το ίδιο Email, ΑΜΚΑ ή AT
    $stmt = $conn->prepare("SELECT * FROM xristis x LEFT JOIN asthenis a ON x.AT = a.AT WHERE x.Email = ? OR a.P_AMKA = ? OR x.AT = ?");
    $stmt->bind_param("sss", $email, $amka, $at);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Υπάρχει ήδη χρήστης με αυτό το Email, ΑΜΚΑ ή AT
        echo "Error: Ο χρήστης υπάρχει ήδη.";
    } else {
        // Ξεκινάμε μια συναλλαγή για να διασφαλίσουμε την ακεραιότητα των δεδομένων
        $conn->begin_transaction();

        try {
            // Εισαγωγή νέου χρήστη στον πίνακα `xristis`
            $sql = "INSERT INTO xristis (FirstName, LastName, Email, Password, Role, AT) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $firstName, $lastName, $email, $password, $role, $at);

            if ($stmt->execute()) {
                // Πήραμε το AT από την τελευταία εισαγωγή
                $new_at = $stmt->insert_id;

                // Εισαγωγή στον πίνακα `asthenis` με βάση το νέο AT
                $sql_patient = "INSERT INTO asthenis (P_AMKA, AT, P_Name, P_Surname, P_DateOfEntry) VALUES (?, ?, ?, ?, NOW())";
                $stmt_patient = $conn->prepare($sql_patient);
                $stmt_patient->bind_param("ssss", $amka, $at, $firstName, $lastName);

                if ($stmt_patient->execute()) {
                    // Επιτυχής εγγραφή και στους δύο πίνακες
                    echo "Ο ασθενής εγγράφηκε επιτυχώς!";
                    $conn->commit(); // Επιβεβαίωση της συναλλαγής
                    exit();
                } else {
                    // Σφάλμα κατά την εισαγωγή στον πίνακα `asthenis`
                    throw new Exception("Σφάλμα κατά την εισαγωγή στον πίνακα ασθενών: " . $stmt_patient->error);
                }
                $stmt_patient->close();
            } else {
                // Σφάλμα κατά την εισαγωγή στον πίνακα `xristis`
                throw new Exception("Σφάλμα κατά την εγγραφή: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback(); // Ακύρωση της συναλλαγής σε περίπτωση σφάλματος
            echo $e->getMessage(); // Εμφάνιση του σφάλματος
        }

        $stmt->close();
    }

    $stmt_check->close();
}

$conn->close();
?>


<!doctype html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Γραμματεία - Διαχείριση Ασθενών</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Γραμματεία - Διαχείριση Ασθενών</h1>

    <!-- Εγγραφή νέου ασθενούς -->
    <section class="patients section">
    <div class="container mt-5">
    <h1 class="text-center">Εγγραφή Νέου Χρήστη</h1>

    <form action="secretary_dashboard.php" method="POST">
        <div class="form-group">
            <label for="first_name">Όνομα</label>
            <input type="text" id="first_name" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="last_name">Επώνυμο</label>
            <input type="text" id="last_name" name="last_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="amka">ΑΜΚΑ</label>
            <input type="text" id="amka" name="amka" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="at">Αριθμός Ταυτότητας (AT)</label>
            <input type="text" id="at" name="at" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Κωδικός Πρόσβασης</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Εγγραφή Χρήστη</button>
    </form>
</div>
    </section>

    <!-- Λίστα ασθενών -->
    <section class="patients-list section mt-5">
        <div class="container">
            <h2>Λίστα Ασθενών</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Όνομα</th>
                        <th>Επώνυμο</th>
                        <th>Email</th>
                        <th>ΑΜΚΑ</th>
                        <th>Ραντεβού</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($patients->num_rows > 0) {
                        while ($row = $patients->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['FirstName']}</td>
                                    <td>{$row['LastName']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>{$row['AT']}</td>
                                    <td><a href='?patient_email={$row['Email']}' class='btn btn-info'>Δείτε Ραντεβού</a></td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Δεν βρέθηκαν ασθενείς</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Ραντεβού συγκεκριμένου ασθενούς -->
    <?php if (!empty($patient_appointments)): ?>
    <section class="appointments section mt-5">
        <div class="container">
            <h2>Ραντεβού Ασθενούς</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ημερομηνία</th>
                        <th>Ώρα</th>
                        <th>Περιγραφή</th>
                        <th>Κατάσταση</th>
                        <th>Γιατρός</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patient_appointments as $appointment): ?>
                        <tr>
                            <td><?= $appointment['a_date'] ?></td>
                            <td><?= $appointment['a_time'] ?></td>
                            <td><?= $appointment['a_desc'] ?></td>
                            <td><?= $appointment['a_state'] ?></td>
                            <td><?= $appointment['a_doc'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
