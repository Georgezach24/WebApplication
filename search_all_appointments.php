<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Ensure the user is logged in and has the role of Doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
    header('Location: login.html');
    exit();
}

// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If search criteria are submitted
$whereClause = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_from = $_POST['date_from'] ?? '';
    $date_to = $_POST['date_to'] ?? '';
    $state = $_POST['state'] ?? '';

    // Add search criteria to the query
    if ($date_from && $date_to) {
        $whereClause .= " AND a_date BETWEEN '$date_from' AND '$date_to'";
    }

    if ($state) {
        $whereClause .= " AND a_state = '$state'";
    }
}

// Fetch all appointments with search criteria
$appointments = $conn->query("
    SELECT id_appointment, a_date, a_time, a_desc, a_state 
    FROM rantevou 
    WHERE 1=1 $whereClause
");

?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Αναζήτηση Όλων των Ραντεβού - Mediplus</title>

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
            <h2 class="text-center">Αναζήτηση Όλων των Ραντεβού</h2>
        </div>
    </div>
</header>

<!-- Search Criteria Form Section -->
<section class="search-criteria section">
    <div class="container">
        <form action="search_all_appointments.php" method="POST" class="form-inline row mb-4 justify-content-center">
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="date_from" class="mr-2">Από Ημερομηνία:</label>
                <input type="date" id="date_from" name="date_from" class="form-control">
            </div>
            <div class="form-group col-lg-3 col-md-4 mb-2">
                <label for="date_to" class="mr-2">Μέχρι Ημερομηνία:</label>
                <input type="date" id="date_to" name="date_to" class="form-control">
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

<!-- Results Section -->
<section class="appointments section">
    <div class="container">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ημερομηνία</th>
                    <th>Ώρα</th>
                    <th>Περιγραφή</th>
                    <th>Κατάσταση</th>
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
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>Δεν βρέθηκαν ραντεβού</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<?php
$conn->close();
?>
