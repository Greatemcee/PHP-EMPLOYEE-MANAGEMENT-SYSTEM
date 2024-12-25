<?php
session_start(); // Start session to handle error messages

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive and sanitize inputs from the form
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['signup_error_message'] = "Passwords do not match!";
        header("Location: index.php?signup=true");
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email or username already exists
    $stmt = $conn->prepare("SELECT id FROM applicants WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email or username already exists
        $_SESSION['signup_error_message'] = "Email or username already exists!";
        header("Location: index.php?signup=true");
        exit();
    } else {
        // Insert the user data into the database using a prepared statement
        $stmt = $conn->prepare("INSERT INTO applicants (Lname, Fname, username, email, address, dob, gender, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $last_name, $first_name, $username, $email, $address, $dob, $gender, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "New account created successfully!";
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['signup_error_message'] = "Error: " . $stmt->error;
            header("Location: index.php?signup=true");
            exit();
        }
    }

    $stmt->close();
}

$conn->close();
?>