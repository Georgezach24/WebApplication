<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Secretary
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Secretary') {
    header('Location: login.html');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch list of appointments and patients
$appointments = $conn->query("SELECT id_appointment, a_date, a_time, a_desc, a_state, a_doc FROM rantevou");
$patients = $conn->query("SELECT Email, FirstName, LastName, AT FROM xristis");

?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Secretary Dashboard - Mediplus</title>

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

        .appointments, .patients {
            margin-bottom: 50px;
        }

        .appointments h3, .patients h3 {
            margin-bottom: 20px;
        }

        .appointments p, .patients p {
            margin-bottom: 15px;
        }

        .appointments a, .patients a {
            margin-top: 20px;
        }

        /* Styling for tables */
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

        .appointments, .patients {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .appointments p, .patients p {
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
                                    <li class="active"><a href="secretary_dashboard.php">Dashboard Γραμματείας</a></li>
                                    <li><a href="manage_appointments.php">Διαχείριση Ραντεβού</a></li>
                                    <li><a href="manage_patients.php">Διαχείριση Ασθενών</a></li>
                                </ul>
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
                    <th>Γιατρός</th>
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
                                <td>{$row['a_doc']}</td>
                                <td>
                                    <a href='edit_appointment.php?id={$row['id_appointment']}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_appointment.php?id={$row['id_appointment']}' class='btn btn-danger'>Ακύρωση</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Δεν βρέθηκαν ραντεβού</td></tr>";
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
                    <th>Email</th>
                    <th>ΑΤ</th>
                    <th>Ενέργειες</th>
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
                                <td>
                                    <a href='edit_patient.php?id={$row['Email']}' class='btn'>Επεξεργασία</a> |
                                    <a href='delete_patient.php?id={$row['Email']}' class='btn btn-danger'>Διαγραφή</a>
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
