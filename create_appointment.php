<?php
session_start();
require_once 'db_config.php';

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος ως ασθενής
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email']; // Email του συνδεδεμένου ασθενούς
$patientAT = ''; // Η ταυτότητα του ασθενούς

// Σύνδεση με τη βάση δεδομένων
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Λήψη του AT (ΑΜΚΑ) του ασθενούς με βάση το email
$stmt = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($patientAT);
$stmt->fetch();
$stmt->close();

// Ανάκτηση της λίστας των γιατρών
$doctors = [];
$stmt = $conn->prepare("SELECT D_id, D_Name, D_Surname FROM iatros");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

$stmt->close();

// Χειρισμός της υποβολής του ραντεβού
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    $doctor_id = $_POST['doctor']; // ID του επιλεγμένου γιατρού

    // Έλεγχος για κενά πεδία
    if (empty($date) || empty($time) || empty($description) || empty($doctor_id)) {
        $error = "Όλα τα πεδία είναι υποχρεωτικά.";
    } else {
        // Εισαγωγή του ραντεβού στον πίνακα rantevou
        $stmt = $conn->prepare("INSERT INTO rantevou (a_date, a_time, a_desc, a_doc, a_state) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssss", $date, $time, $description, $doctor_id);

        if ($stmt->execute()) {
            // Λήψη του ID του νέου ραντεβού
            $appointment_id = $stmt->insert_id;

            // Σύνδεση του ραντεβού με τον ασθενή στον πίνακα books
            $stmt_books = $conn->prepare("INSERT INTO books (AT, id_appointment) VALUES (?, ?)");
            $stmt_books->bind_param("ss", $patientAT, $appointment_id);

            if ($stmt_books->execute()) {
                $success = "Το ραντεβού δημιουργήθηκε επιτυχώς.";
                header("Location: success.html");
            } else {
                $error = "Σφάλμα κατά τη σύνδεση του ραντεβού με τον ασθενή.";
            }
            $stmt_books->close();
        } else {
            $error = "Σφάλμα κατά τη δημιουργία του ραντεβού: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!doctype html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Δημιουργία Νέου Ραντεβού - Mediplus</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/icofont.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Header Area -->
<header class="header">
    <div class="header-inner">
        <div class="container">
            <div class="inner">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-12">
                        <!-- Logo -->
                        <div class="logo">
                            <a href="index.html"><img src="img/logo.png" alt="#"></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-12">
                        <div class="get-quote">
                            <a href="logout.php" class="btn">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End Header Area -->

<!-- Create Appointment Section -->
<section class="create-appointment section">
    <div class="container">
        <div class="section-title">
            <h2>Δημιουργία Νέου Ραντεβού</h2>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-12 col-12">
                <form action="" method="post">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="date">Ημερομηνία:</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Ώρα:</label>
                        <input type="time" id="time" name="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Περιγραφή:</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="doctor">Επιλέξτε Γιατρό:</label>
                        <select id="doctor" name="doctor" class="form-control" required>
                            <option value="">-- Επιλέξτε Γιατρό --</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['D_id']; ?>">
                                    <?php echo $doctor['D_Name'] . " " . $doctor['D_Surname']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn primary">Δημιουργία Ραντεβού</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer Area -->
<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-footer footer-contact">
                        <h2>Επικοινωνία</h2>
                        <p>Επικοινωνήστε μαζί μας για οποιαδήποτε απορία ή ζήτηση για ραντεβού.</p>
                        <p><i class="icofont-email"></i> support@clinic.com</p>
                        <p><i class="icofont-phone"></i> +30 210 1234567</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End Footer -->

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

