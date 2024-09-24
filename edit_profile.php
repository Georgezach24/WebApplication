<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];

// Create a new database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for updating the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newAmka = $_POST['amka'];

    // Start transaction to update both xristis and asthenis tables
    $conn->begin_transaction();

    try {
        // Update the user's first name and last name in the xristis table
        $stmt = $conn->prepare("UPDATE xristis SET FirstName = ?, LastName = ? WHERE Email = ?");
        $stmt->bind_param("sss", $newFirstName, $newLastName, $email);
        $stmt->execute();
        $stmt->close();

        // Fetch the patient's AT (ID) from the xristis table to update the asthenis table
        $stmt = $conn->prepare("SELECT AT FROM xristis WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($patientAT);
        $stmt->fetch();
        $stmt->close();

        // Update the patient's AMKA in the asthenis table using the patient's AT
        $stmt = $conn->prepare("UPDATE asthenis SET P_AMKA = ? WHERE AT = ?");
        $stmt->bind_param("ss", $newAmka, $patientAT);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction if both updates are successful
        $conn->commit();

        // Update the session data and redirect
        $_SESSION['first_name'] = $newFirstName;
        $_SESSION['last_name'] = $newLastName;
        echo "<script>alert('Profile updated successfully!'); window.location.href='patient_dashboard.php';</script>";
    } catch (Exception $e) {
        // Roll back the transaction if there was an error
        $conn->rollback();
        echo "<script>alert('Error updating profile. Please try again.'); window.location.href='edit_profile.php';</script>";
    }
} else {
    // Fetch the user's first name and last name from the xristis table
    $stmt = $conn->prepare("SELECT FirstName, LastName, AT FROM xristis WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $patientAT);
    $stmt->fetch();
    $stmt->close();

    // Fetch the user's AMKA from the asthenis table using the patient's AT
    $stmt = $conn->prepare("SELECT P_AMKA FROM asthenis WHERE AT = ?");
    $stmt->bind_param("s", $patientAT);
    $stmt->execute();
    $stmt->bind_result($amka);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-primary {
            margin-top: 10px;
        }
        .header {
            margin-bottom: 30px;
        }
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Header Area -->
<header class="header">
    <div class="header-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>Edit Your Profile</h1>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End Header Area -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center">Update Profile Information</h2>
                    
                    <form action="edit_profile.php" method="POST">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="amka">AMKA</label>
                            <input type="text" class="form-control" id="amka" name="amka" value="<?php echo htmlspecialchars($amka); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                    </form>

                    <a href="patient_dashboard.php" class="btn btn-link d-block text-center mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <div class="text-center">
            <p>Mediplus &copy; 2024. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
