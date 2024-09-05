<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Patient') {
    header('Location: login.html');
    exit();
}

$email = $_SESSION['user_email'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];

    $stmt = $conn->prepare("UPDATE xristis SET FirstName = ?, LastName = ? WHERE Email = ?");
    $stmt->bind_param("sss", $newFirstName, $newLastName, $email);

    if ($stmt->execute()) {
        $_SESSION['first_name'] = $newFirstName;
        $_SESSION['last_name'] = $newLastName;
        echo "<script>alert('Profile updated successfully!'); window.location.href='patient_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.'); window.location.href='edit_profile.php';</script>";
    }

    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT FirstName, LastName FROM xristis WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName);
    $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <form action="edit_profile.php" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $firstName; ?>" required>
        <br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $lastName; ?>" required>
        <br>
        <button type="submit">Update Profile</button>
    </form>
    <br>
    <a href="patient_dashboard.php">Back to Dashboard</a>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
