<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

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
                                <ul class="nav menu">
                                    <li><a href="index.php">Αρχική</a></li>
                                    <li class="active"><a href="patient_dashboard.php">Προφίλ Ασθενούς</a></li>
                                    <li><a href="appointments.php">Ραντεβού</a></li>
                                    <li><a href="medical_history.php">Ιστορικό</a></li>
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
                $stmt = $conn->prepare("SELECT id_appointment, a_date, a_time, a_desc, a_state FROM rantevou WHERE a_doc = ?");
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
