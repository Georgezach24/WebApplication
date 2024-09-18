<?php
session_start();
require_once 'db_config.php'; // Include your database configuration file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Getting the email from the form
    $password = $_POST['password']; // Getting the password from the form

    // Create the connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query to fetch the user by email
    $stmt = $conn->prepare("SELECT Email, Password, FirstName, LastName, Role FROM xristis WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result to variables
        $stmt->bind_result($db_email, $db_password, $firstName, $lastName, $role);
        $stmt->fetch();

        // Use password_verify to compare the entered password with the hashed password in the database
        if (password_verify($password, $db_password)) { // Check hashed password
            // Successful login, store user info in session
            $_SESSION['user_email'] = $db_email;
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['role'] = $role;

            // Redirect based on the role
            switch ($role) {
                case 'Doctor':
                    header("Location: doctor_dashboard.php");
                    break;
                case 'Secretary':
                    header("Location: secretary_dashboard.php");
                    break;
                case 'Patient':
                    header("Location: patient_dashboard.php");
                    break;
                default:
                    header("Location: index.html");
                    break;
            }
            exit();
        } else {
            // Invalid password
            echo "<script>alert('Λάθος κωδικός πρόσβασης. Παρακαλώ δοκιμάστε ξανά.'); window.location.href='login.html';</script>";
        }
    } else {
        // Invalid email
        echo "<script>alert('Ο χρήστης δεν βρέθηκε. Παρακαλώ δοκιμάστε ξανά.'); window.location.href='login.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
