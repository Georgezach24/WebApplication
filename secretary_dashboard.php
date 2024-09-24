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

$conn->close();
?>

<!doctype html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Γραμματεία - Διαχείριση Ασθενών</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
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
                                <a href="logout.php" class="btn btn-logout">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header Area -->

    <h1 class="text-center">Γραμματεία - Διαχείριση Ασθενών</h1>

    <!-- Εγγραφή νέου ασθενούς -->
    <section class="patients section">
        <div class="container mt-5">
            <h2 class="text-center">Εγγραφή Νέου Χρήστη</h2>

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
                        <th>AT</th>
                        <th>Ραντεβού</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($patients->num_rows > 0) {
                        while ($row = $patients->fetch_assoc()) {
                            $collapseId = "appointments" . $row['Email']; // Unique ID for each patient
                            echo "<tr>
                                    <td>{$row['FirstName']}</td>
                                    <td>{$row['LastName']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>{$row['P_AMKA']}</td>
                                    <td>{$row['AT']}</td>
                                    <td><button class='btn btn-info show-appointments' data-email='{$row['Email']}'>Δείτε Ραντεβού</button></td>
                                  </tr>";

                            // Placeholder για τα ραντεβού
                            echo "<tr><td colspan='6'>
                                    <div id='{$collapseId}' class='appointment-details'></div>
                                  </td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Δεν βρέθηκαν ασθενείς</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
    $('.show-appointments').click(function() {
        const email = $(this).data('email');
        const targetDiv = $(this).closest('tr').next().find('.appointment-details');

        // Έλεγχος αν το div είναι ήδη ανοιχτό
        if (targetDiv.is(':visible')) {
            // Αν είναι ανοιχτό, το κλείνουμε
            targetDiv.slideUp();
        } else {
            // Αν είναι κλειστό, κάνουμε AJAX αίτημα για τα ραντεβού
            $.ajax({
                url: 'get_appointments.php',
                type: 'POST',
                data: { patient_email: email },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.error) {
                        targetDiv.html(`<p>${data.error}</p>`);  // Εμφανίζει το σφάλμα αν υπάρχει
                    } else if (data.length > 0) {
                        let html = '<table class="table table-sm"><thead><tr><th>Ημερομηνία</th><th>Ώρα</th><th>Περιγραφή</th><th>Κατάσταση</th><th>Γιατρός</th></tr></thead><tbody>';
                        data.forEach(function(app) {
                            html += `<tr>
                                        <td>${app.a_date}</td>
                                        <td>${app.a_time}</td>
                                        <td>${app.a_desc}</td>
                                        <td>${app.a_state}</td>
                                        <td>${app.a_doc}</td>
                                     </tr>`;
                        });
                        html += '</tbody></table>';
                        targetDiv.html(html);
                    } else {
                        targetDiv.html('<p>Δεν βρέθηκαν ραντεβού.</p>');
                    }
                    targetDiv.slideDown(); // Εμφανίζουμε τα ραντεβού με ομαλό άνοιγμα
                },
                error: function() {
                    targetDiv.html('<p>Σφάλμα κατά τη φόρτωση των ραντεβού.</p>');
                }
            });
        }
    });
});

    </script>
</div>

</body>
</html>

