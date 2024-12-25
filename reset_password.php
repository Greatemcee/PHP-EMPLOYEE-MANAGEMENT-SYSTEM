<?php
session_start(); // Start the session to store user data temporarily

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL timezone to Manila Time (+08:00)
$conn->query("SET time_zone = '+08:00'");
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['id'];
        $_SESSION['reset_user_id'] = $user_id; // Store user ID in session
    } else {
        echo "Invalid or expired token.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['reset_user_id'])) {
    $new_password = $_POST['password'];

    // Validate new password (you can add more rules if needed)
    if (strlen($new_password) < 6) {
        echo "Password must be at least 6 characters long.";
        exit;
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $user_id = $_SESSION['reset_user_id'];

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    $stmt->execute();

    // Check if update was successful
    if ($stmt->affected_rows > 0) {
        echo "Password reset successful. You can now <a href='login.php'>login</a>.";
    } else {
        echo "Failed to update password. Please try again.";
    }

    // Clear session data for security
    unset($_SESSION['reset_user_id']);
}
?>

<!-- HTML form for entering new password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

    <form method="POST" action="reset_password.php">
        <h2>Reset Password</h2>
        <input type="password" name="password" placeholder="New Password" required />
        <button type="submit">Reset Password</button>
        <button id="back-to-login">Back to Login</button>
    </form>

    <script src="script.js"></script>
</body>
</html>