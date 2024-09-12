<?php
session_start();
require_once 'db_config.php'; // Συμπεριλαμβάνουμε το αρχείο σύνδεσης με τη βάση δεδομένων

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος και έχει το ρόλο της γραμματείας
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Secretary') {
    header('Location: login.html');
    exit();
}

// Δημιουργία σύνδεσης με τη βάση δεδομένων
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Χειρισμός της εγγραφής νέου ασθενούς
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_patient'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $amka = $_POST['amka'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Κρυπτογράφηση του κωδικού

    // Έλεγχος αν υπάρχει ήδη ο χρήστης με το ίδιο ΑΜΚΑ ή email
    $check_sql = "SELECT * FROM xristis WHERE AT = ? OR Email = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param('ss', $amka, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo "Υπάρχει ήδη ασθενής με αυτό το ΑΜΚΑ ή email.";
    } else {
        // Εισαγωγή νέου ασθενούς
        $sql = "INSERT INTO xristis (AT, FirstName, LastName, Email, Password, Role) VALUES (?, ?, ?, ?, ?, 'Patient')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $amka, $first_name, $last_name, $email, $password);

        if ($stmt->execute()) {
            // Επιτυχής εγγραφή
            echo "Ο ασθενής εγγράφηκε επιτυχώς!";
        } else {
            // Σφάλμα κατά την εγγραφή
            echo "Σφάλμα κατά την εγγραφή: " . $conn->error;
        }
        $stmt->close();
    }

    $stmt_check->close();
}


// Φόρτωση λίστας ασθενών
$patients = $conn->query("SELECT Email, FirstName, LastName, AT FROM xristis WHERE Role = 'Patient'");

// Χειρισμός εμφάνισης ραντεβού συγκεκριμένου ασθενούς
$patient_appointments = [];
if (isset($_GET['patient_email'])) {
    $patient_email = $_GET['patient_email'];
    $patient_appointments_query = $conn->query("SELECT * FROM rantevou WHERE patient_email = '$patient_email'");
    while ($row = $patient_appointments_query->fetch_assoc()) {
        $patient_appointments[] = $row;
    }
}
?>

<!doctype html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Patient Dashboard - Mediplus</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/icofont.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Custom styling for better layout */
        .header-inner {
            margin-bottom: 30px;
        }

        /* Remove the topbar and extra contact information */
        .topbar, .top-contact {
            display: none;
        }

        .section-title {
            margin-bottom: 40px;
            text-align: center;
        }

        .profile-info, .appointments, .history {
            margin-bottom: 50px;
        }

        .profile-info h3, .appointments h3, .history h3 {
            margin-bottom: 20px;
        }

        .profile-info p, .appointments p, .history p {
            margin-bottom: 15px;
        }

        .profile-info a {
            margin-top: 20px;
        }

        /* Styling for table */
        .table-responsive {
            margin-top: 20px;
        }

        /* Footer styling */
        footer {
            margin-top: 50px;
            padding: 30px 0;
            background: #f8f9fa;
        }

        /* Spacing for navigation links */
        .nav.menu > li {
            margin-right: 20px;
        }

        .profile-info, .appointments, .history {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .profile-info p, .appointments p, .history p {
            font-size: 16px;
            color: #333;
        }

        /* Add subtle separator line between menu and content */
        .nav-separator {
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        /* Footer links and contact information layout */
        .footer-bottom {
            margin-top: 30px;
        }

        /* Hide the unnecessary "Links" section in footer */
        .footer-links {
            display: none;
        }

        .footer-contact p {
            margin-bottom: 10px;
        }

        .footer-contact i {
            margin-right: 10px;
        }

        .btn {
            margin-top: 10px;
        }
    </style>
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
                            <!-- Subtle separator line -->
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

<!-- Εγγραφή νέου ασθενούς -->
<section class="patients section">
    <div class="container">
        <h2>Εγγραφή Νέου Ασθενούς</h2>
        <form action="patient_register.php" method="POST">
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
                <label for="password">Κωδικός Πρόσβασης</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="register_patient" class="btn btn-primary">Εγγραφή Ασθενούς</button>
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

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
