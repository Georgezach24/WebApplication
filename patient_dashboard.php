<?php
session_start();
require_once 'db_config.php'; 

// Ensure the user is logged in and has the role of Patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch patient information
$stmt = $conn->prepare("SELECT AT, FirstName, LastName FROM xristis WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($patientAT, $firstName, $lastName);
$stmt->fetch();
$stmt->close();
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Patient Dashboard - Mediplus</title>

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
                            <a href="index.php"><img src="img/logo.png" alt="#"></a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-9 col-12">
                        <div class="main-menu">
                            <nav class="navigation">
                            </nav>
                            <div class="nav-separator"></div>
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

<!-- Profile Section -->
<section class="profile section">
    <div class="container">
        <div class="section-title">
            <h2>Προφίλ Ασθενούς</h2>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-12">
                <div class="profile-info">
                    <h3>Στοιχεία Προφίλ</h3>
                    <p><strong>Όνομα:</strong> <?php echo $firstName . " " . $lastName; ?></p>
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                    <a href="edit_profile.php" class="btn primary">Επεξεργασία Προφίλ</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Appointments Section -->
<section class="appointments section">
    <div class="container">
        <div class="section-title">
            <h2>Τα Ραντεβού Μου</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ημερομηνία</th>
                    <th>Ώρα</th>
                    <th>Περιγραφή</th>
                    <th>Κατάσταση</th>
                    <th>Ενέργειες</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Fetch appointments for the logged-in patient
                $stmt = $conn->prepare("
                    SELECT r.id_appointment, r.a_date, r.a_time, r.a_desc, r.a_state 
                    FROM rantevou r
                    JOIN books b ON r.id_appointment = b.id_appointment
                    WHERE b.AT = ?");
                $stmt->bind_param("s", $patientAT);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id_appointment, $date, $time, $desc, $state);

                if ($stmt->num_rows > 0) {
                    while ($stmt->fetch()) {
                        echo "<tr>
                                <td>{$date}</td>
                                <td>{$time}</td>
                                <td>{$desc}</td>
                                <td>{$state}</td>
                                <td>
                                    <a href='edit_appointment.php?id={$id_appointment}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_appointment.php?id={$id_appointment}' class='btn btn-danger'>Ακύρωση</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Δεν βρέθηκαν ραντεβού</td></tr>";
                }
                $stmt->close();
                ?>
                </tbody>
            </table>
        </div>
        <a href="create_appointment.php" class="btn primary">Δημιουργία Νέου Ραντεβού</a>
    </div>
</section>

<!-- Medical History Section -->
<section class="history section">
    <div class="container">
        <div class="section-title">
            <h2>Ιατρικό Ιστορικό</h2>
        </div>
        <div class="row">
            <?php
            // Fetch medical history for the patient
            $stmt_history = $conn->prepare("SELECT id_entry, e_doc, e_healthproblems, e_cure FROM eggrafi WHERE id_entry = ?");
            $stmt_history->bind_param("s", $patientAT);
            $stmt_history->execute();
            $stmt_history->store_result();
            $stmt_history->bind_result($id_entry, $doc, $problems, $cure);

            if ($stmt_history->num_rows > 0) {
                while ($stmt_history->fetch()) {
                    echo "<div class='col-lg-6 col-md-12'>
                            <div class='history-entry'>
                                <p><strong>Γιατρός:</strong> {$doc}</p>
                                <p><strong>Προβλήματα Υγείας:</strong> {$problems}</p>
                                <p><strong>Θεραπεία:</strong> {$cure}</p>
                            </div>
                        </div>";
                }
            } else {
                echo "<p>Δεν βρέθηκαν καταχωρήσεις στο ιστορικό.</p>";
            }
            $stmt_history->close();
            ?>
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

<?php
$conn->close();
?>
