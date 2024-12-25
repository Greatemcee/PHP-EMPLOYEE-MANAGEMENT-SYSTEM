<?php
session_start();

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management_system";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Prepare a statement to select the user based on the entered email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with that email exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Log role value for debugging
        error_log("Role retrieved: " . $row['role']);

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['emp_id'] = $row['Emp_ID']; // Add Employee ID to the session

            // Debugging: Print the Emp_ID
            error_log("Employee ID: " . $row['Emp_ID']);

            // Redirect based on the role
            if ($row['role'] === 'Admin') {
                header("Location: admin/admindashboard.php");
                exit();
            } elseif (trim($row['role']) === 'Employee') {
                error_log("Redirecting to Employee Dashboard...");
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['login_error_message'] = "Invalid role assigned!";
            }
        } else {
            $_SESSION['login_error_message'] = "INVALID ACCOUNT!";
        }
    } else {
        $_SESSION['login_error_message'] = "INVALID ACCOUNT!";
    }

    $stmt->close();
    $conn->close();
}

// Redirect back to the login page if there was an error
header("Location: index.php");
exit();
?>
