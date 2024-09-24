<?php
session_start();
require_once 'db_config.php'; 

// Ensure the user is logged in and has the role of Patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Αναζήτηση Ραντεβού - Mediplus</title>

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

<!-- Search Criteria Form Section -->
<section class="search-criteria section">
    <div class="container">
        <div class="section-title">
            <h2>Αναζήτηση Ραντεβού</h2>
        </div>

        <form action="search_appointments.php" method="POST" class="form-inline row mb-4 justify-content-center">
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="date_from" class="mr-2">Από Ημερομηνία:</label>
                <input type="date" id="date_from" name="date_from" class="form-control">
            </div>
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="date_to" class="mr-2">Μέχρι Ημερομηνία:</label>
                <input type="date" id="date_to" name="date_to" class="form-control">
            </div>
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="surname" class="mr-2">Επίθετο:</label>
                <input type="text" id="surname" name="surname" class="form-control">
            </div>
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="amka" class="mr-2">ΑΜΚΑ:</label>
                <input type="text" id="amka" name="amka" class="form-control">
            </div>
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="state" class="mr-2">Κατάσταση:</label>
                <select id="state" name="state" class="form-control">
                    <option value="">Όλες</option>
                    <option value="Pending">Εκκρεμείς</option>
                    <option value="Confirmed">Επιβεβαιωμένες</option>
                    <option value="Canceled">Ακυρωμένες</option>
                </select>
            </div>
            <div class="form-group col-lg-3 col-md-4 mt-4">
                <button type="submit" class="btn btn-primary btn-block">Αναζήτηση</button>
            </div>
        </form>
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
