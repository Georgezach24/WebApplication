<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

$doctor_email = $_SESSION['user_email']; // Get the logged-in doctor's email

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor AT (Αριθμός Ταυτότητας) based on email
$stmt_doc = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
$stmt_doc->bind_param("s", $doctor_email);
$stmt_doc->execute();
$stmt_doc->bind_result($doctorAT);
$stmt_doc->fetch();
$stmt_doc->close();

// Fetch appointments related to the logged-in doctor
$appointments = $conn->query("
    SELECT id_appointment, a_date, a_time, a_desc, a_state 
    FROM rantevou 
    WHERE a_doc = '$doctorAT'
");

// Fetch patients (no join, we retrieve all patients)
$patients = $conn->query("
    SELECT AT, P_Name, P_Surname, P_AMKA 
    FROM asthenis
");

// Fetch medical history related to the logged-in doctor (no join, we just get the entries directly)
$history = $conn->query("
    SELECT id_entry, e_healthproblems, e_cure 
    FROM eggrafi 
    WHERE e_doc = '$doctorAT'
");
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Doctor Dashboard - Mediplus</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/icofont.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Custom styling for better layout */
        .header-inner {
            margin-bottom: 30px;
        }

        .topbar, .top-contact {
            display: none;
        }

        .section-title {
            margin-bottom: 40px;
            text-align: center;
        }

        .appointments, .patients, .history {
            margin-bottom: 50px;
        }

        .appointments h3, .patients h3, .history h3 {
            margin-bottom: 20px;
        }

        .appointments p, .patients p, .history p {
            margin-bottom: 15px;
        }

        .appointments a, .patients a, .history a {
            margin-top: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        footer {
            margin-top: 50px;
            padding: 30px 0;
            background: #f8f9fa;
        }

        .nav.menu > li {
            margin-right: 20px;
        }

        .appointments, .patients, .history {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .appointments p, .patients p, .history p {
            font-size: 16px;
            color: #333;
        }

        .nav-separator {
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .footer-bottom {
            margin-top: 30px;
        }

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
                                <ul class="nav menu">
                                    <li><a href="index.php">Αρχική</a></li>
                                    <li class="active"><a href="doctor_dashboard.php">Dashboard Γιατρού</a></li>
                                    <li><a href="manage_appointments.php">Διαχείριση Ραντεβού</a></li>
                                    <li><a href="manage_patients.php">Διαχείριση Ασθενών</a></li>
                                    <li><a href="manage_history.php">Διαχείριση Ιατρικού Ιστορικού</a></li>
                                </ul>
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

<!-- Appointments Section -->
<section class="appointments section">
    <div class="container">
        <div class="section-title">
            <h2>Διαχείριση Ραντεβού</h2>
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
                if ($appointments->num_rows > 0) {
                    while ($row = $appointments->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['a_date']}</td>
                                <td>{$row['a_time']}</td>
                                <td>{$row['a_desc']}</td>
                                <td>{$row['a_state']}</td>
                                <td>
                                    <a href='edit_appointment.php?id={$row['id_appointment']}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_appointment.php?id={$row['id_appointment']}' class='btn btn-danger'>Ακύρωση</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Δεν βρέθηκαν ραντεβού</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Patients Section -->
<section class="patients section">
    <div class="container">
        <div class="section-title">
            <h2>Διαχείριση Ασθενών</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Όνομα</th>
                    <th>Επώνυμο</th>
                    <th>AMKA</th>
                    <th>ΑΤ</th>
                    <th>Ενέργειες</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($patients->num_rows > 0) {
                    while ($row = $patients->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['P_Name']}</td>
                                <td>{$row['P_Surname']}</td>
                                <td>{$row['P_AMKA']}</td>
                                <td>{$row['AT']}</td>
                                <td>
                                    <a href='edit_patient.php?id={$row['AT']}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_patient.php?id={$row['AT']}' class='btn btn-danger'>Διαγραφή</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Δεν βρέθηκαν ασθενείς</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Medical History Section -->
<section class="history section">
    <div class="container">
        <div class="section-title">
            <h2>Διαχείριση Ιατρικού Ιστορικού</h2>
        </div>
        <div class="table-responsive">
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
                <?php
                if ($history->num_rows > 0) {
                    while ($row = $history->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['e_healthproblems']}</td>
                                <td>{$row['e_cure']}</td>
                                <td>
                                    <a href='edit_history.php?id={$row['id_entry']}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_history.php?id={$row['id_entry']}' class='btn btn-danger'>Διαγραφή</a> |
                                    <a href='export_history.php?id={$row['id_entry']}' class='btn'>Εξαγωγή σε PDF/Excel</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>Δεν βρέθηκαν καταχωρήσεις στο ιστορικό</td></tr>";
                }
                ?>
                </tbody>
            </table>
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

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<?php
$conn->close();
?>
