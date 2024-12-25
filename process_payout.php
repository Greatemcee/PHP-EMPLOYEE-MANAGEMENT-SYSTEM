<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management_system";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$empid = $_SESSION['emp_id'];
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_method = isset($_POST['bank']) ? trim($_POST['bank']) : "";

// Validate payment method
$valid_methods = ['cash', 'e_wallet'];
if (!in_array($payment_method, $valid_methods)) {
    header("Location: dashboard.php?page=payout&error=invalid_payment_method");
    exit();
}

// Fetch user's current balance
$query = "SELECT balance FROM users WHERE Emp_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $empid); // Emp_ID as parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $current_balance = floatval($user['balance']);

    // Check if the requested amount is valid
    if ($amount > 0 && $amount <= $current_balance) {
        // Deduct the amount from the user's balance
        $new_balance = $current_balance - $amount;

        // Update the balance in the database
        $update_balance_query = "UPDATE users SET balance = ? WHERE Emp_ID = ?";
        $stmt = $conn->prepare($update_balance_query);
        $stmt->bind_param("ds", $new_balance, $empid); // Bind new_balance as double and Emp_ID as string

        if ($stmt->execute()) {
            // Log the transaction with the payment method
            $insert_payout_query = "INSERT INTO payouts (user_id, amount, date, Emp_ID, method) VALUES (?, ?, NOW(), ?, ?)";
            $stmt = $conn->prepare($insert_payout_query);
            $stmt->bind_param("idss", $user_id, $amount, $empid, $payment_method);

            if ($stmt->execute()) {
                // Redirect with success
                header("Location: dashboard.php?page=payout&success=1&balance=" . number_format($new_balance, 2));
                exit();
            } else {
                // Log the error if transaction logging fails
                die("Transaction logging error: " . $stmt->error);
            }
        } else {
            // Log the error if balance update fails
            die("Balance update error: " . $stmt->error);
        }
    } else {
        // Redirect with error if invalid amount
        header("Location: dashboard.php?page=payout&error=invalid_amount&balance=" . number_format($current_balance, 2));
        exit();
    }
} else {
    // Redirect with error if user not found
    header("Location: dashboard.php?page=payout&error=user_not_found");
    exit();
}

$stmt->close();
$conn->close();
?>
