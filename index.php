<?php
session_start();

// If user is not logged in, redirect to the login page
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}
?>
<!doctype html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Health Clinic - Αρχική</title>

        <!-- CSS and Bootstrap -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <!-- Your header content here -->
        </header>

        <div class="container">
            <h1>Καλωσορίσατε στο Health Clinic</h1>
            <p>Επιτυχής σύνδεση, καλωσορίσατε <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</p>
            <p>Email: <?php echo $_SESSION['user_email']; ?></p>
        </div>

        <!-- JS and Bootstrap -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
