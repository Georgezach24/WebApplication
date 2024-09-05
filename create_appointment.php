<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];
$patientAT = '';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch patient information to get the AT
$stmt = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($patientAT);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    
    // Validate input
    if (empty($date) || empty($time) || empty($description)) {
        $error = "All fields are required.";
    } else {
        // Insert new appointment into the database
        $stmt = $conn->prepare("INSERT INTO rantevou (a_doc, a_date, a_time, a_desc, a_state) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssss", $patientAT, $date, $time, $description);
        
        if ($stmt->execute()) {
            $success = "Appointment created successfully.";
        } else {
            $error = "Error creating appointment: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

<!doctype html>
<html class="no-js" lang="zxx">
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
                            <a href="index.php"><img src="img/logo.png" alt="#"></a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-9 col-12">
                        <div class="main-menu">
                            <nav class="navigation">
                                <ul class="nav menu">
                                    <li><a href="index.php">Αρχική</a></li>
                                    <li><a href="patient_dashboard.php">Προφίλ Ασθενούς</a></li>
                                    <li><a href="appointments.php">Ραντεβού</a></li>
                                    <li><a href="medical_history.php">Ιστορικό</a></li>
                                </ul>
                            </nav>
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

<?php
$conn->close();
?>
