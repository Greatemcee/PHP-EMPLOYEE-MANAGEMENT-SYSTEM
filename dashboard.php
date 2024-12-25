<?php
session_start(); // Start the session to access session variables

// If the user is not logged in, redirect them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

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

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];
$empid = $_SESSION['emp_id'];

// Initialize error message
$error_message = '';

date_default_timezone_set('Asia/Manila'); // e.g., 'Asia/Manila'


// Handle profile updates if POST request is detected and 'save-settings' button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-settings'])) {
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $username = $_POST['username'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Check if passwords match if a new password is provided
    if (!empty($password) && $password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password only if a new password is provided
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

        // Handle profile picture upload
        if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == 0) {
            $target_dir = "uploads/"; // Ensure this directory exists and is writable
            $filename = $user_id . "_" . basename($_FILES['profile-pic']['name']); // Unique filename
            $target_file = $target_dir . $filename;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES['profile-pic']['tmp_name'], $target_file)) {
                    $profile_pic_path = $target_file;

                    // Update profile picture path in the database
                    $update_pic_query = "UPDATE users SET profile_pic = ? WHERE id = ?";
                    $stmt = $conn->prepare($update_pic_query);
                    $stmt->bind_param("si", $profile_pic_path, $user_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $error_message = "Error uploading profile picture.";
                }
            } else {
                $error_message = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }

        // Prepare query for updating user information
        if ($hashed_password) {
            // Include password in the update
            $update_query = "UPDATE users SET first_name = ?, last_name = ?, username = ?, dob = ?, gender = ?, email = ?, address = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssssssi", $first_name, $last_name, $username, $dob, $gender, $email, $address, $hashed_password, $user_id);
        } else {
            // Exclude password if no new password provided
            $update_query = "UPDATE users SET first_name = ?, last_name = ?, username = ?, dob = ?, gender = ?, email = ?, address = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssssi", $first_name, $last_name, $username, $dob, $gender, $email, $address, $user_id);
        }

        // Execute the update and handle redirection
        if ($stmt->execute()) {
            header("Location: dashboard.php?page=settings&success=1");
            exit();
        } else {
            $error_message = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch the updated balance from the database to ensure it reflects the latest changes
$query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $balance = floatval($user_data['balance']); // Get the most recent balance
} else {
    $balance = 0.00; // Set default balance if no record is found
}
$stmt->close();




/*if (isset($_GET['success'])) {
    echo "<p style='color: green;'>Withdrawal successful! Your new balance is ₱" . htmlspecialchars($_GET['balance']) . "</p>";
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_amount':
            echo "<p style='color: red;'>Invalid withdrawal amount. Ensure it's within your balance.</p>";
            break;
        case 'invalid_payment_method':
            echo "<p style='color: red;'>Invalid payment method selected. Please choose a valid method.</p>";
            break;
        case 'database_error':
            echo "<p style='color: red;'>An error occurred while processing your request. Please try again.</p>";
            break;
        case 'user_not_found':
            echo "<p style='color: red;'>User not found. Please contact support.</p>";
            break;
        default:
            echo "<p style='color: red;'>An unknown error occurred.</p>";
            break;
    }
}*/

// Fetch user information, including profile picture
$query = "SELECT last_name, first_name, username, email, address, dob, gender, password, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Set profile picture path
$profile_pic_path = $user['profile_pic'] ?? 'uploads/default.jpg';


// Fetch the total hours worked from the attendance table
// Fetch total worked hours
$query = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(time_out, time_in)))) AS total_hours FROM attendance WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_hours = $row['total_hours'] ?? '00:00:00'; // If no time worked, set to '00:00:00'
$stmt->close();

// Fetch salary rate and current balance
$query = "SELECT salary_rate, balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$salary_rate = $user['salary_rate'] ?? 100; // Default salary rate if not found
$current_balance = $user['balance'] ?? 0; // Default balance if not found
$stmt->close();

// Validate total_hours (only update balance if total_hours is not zero or invalid)
/*if ($total_hours !== '00:00:00' && $total_hours !== '00:00:01' && $total_hours !== '00:00:00.000') {
    // Ensure total_hours format is valid, e.g., 'hh:mm:ss'
    if (preg_match('/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $total_hours, $matches)) {
        // Extract hours, minutes, and seconds from the match
        list($hours, $minutes, $seconds) = explode(":", $total_hours);
        $total_hours_in_decimal = $hours + ($minutes / 60) + ($seconds / 3600);

        // Ensure the user has worked (total_hours_in_decimal must be > 0)
        if ($total_hours_in_decimal > 0) {
            // Calculate total salary for worked hours
            $total_salary = $salary_rate * $total_hours_in_decimal;

            // Add the total salary to the user's current balance
            $new_balance = $current_balance + $total_salary;
            $new_balance = round($new_balance, 2);

            // Update the balance in the database
            $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
            $stmt = $conn->prepare($update_balance_query);
            $stmt->bind_param("di", $new_balance, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}*/



// Fetch user information, including profile picture
$query = "SELECT last_name, first_name, username, email, address, dob, gender, password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch the total number of shifts the user attended (status: 'Present' or 'Late')
$query = "SELECT COUNT(*) AS total_shifts FROM attendance WHERE id = ? AND status IN ('Present', 'Late')";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_shifts = $row['total_shifts'] ?? 0;
$stmt->close();

// Mark past shifts as "Absent" if they were not timed in
$today = date("Y-m-d");

// Query to find past shifts that are not recorded in the attendance table
$absent_check_query = "
    SELECT date, shift_type 
    FROM shift 
    WHERE id = ? AND date < ? 
    AND date NOT IN (SELECT date FROM attendance WHERE id = ?)";
$stmt = $conn->prepare($absent_check_query);
$stmt->bind_param("isi", $user_id, $today, $user_id);
$stmt->execute();
$absent_shifts = $stmt->get_result();

// Insert an absence record for each past unrecorded shift
while ($absent_shift = $absent_shifts->fetch_assoc()) {
    $shift_date = $absent_shift['shift_date'];
    $shift_type = $absent_shift['shift_type'];

    $insert_absent_query = "INSERT INTO attendance (id, date, shift_type, status) VALUES (?, ?, ?, 'Absent')";
    $insert_stmt = $conn->prepare($insert_absent_query);
    $insert_stmt->bind_param("iss", $user_id, $shift_date, $shift_type);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'time_in') {
        $selected_shift = $_POST['shift_id'] ?? '';

        if (empty($selected_shift)) {
            $error_message = "Please select a shift before timing in.";
        } else {
            // Extract shift date, time in, and time out from the selected option
            list($shift_date, $shift_type, $shift_time_in, $shift_time_out) = explode('|', $selected_shift);

            // Get current date and time
            $current_date = date("Y-m-d");
            $current_time = date("H:i:s");

            // Convert scheduled shift time and current time into DateTime objects for better accuracy
            $shift_time_in_datetime = new DateTime("$shift_date $shift_time_in");
            $current_time_datetime = new DateTime("$current_date $current_time");

            // Calculate the time difference in seconds
            $time_diff = $current_time_datetime->getTimestamp() - $shift_time_in_datetime->getTimestamp();

            // Debugging output (can be removed after validation)
            // echo "Current time: $current_date $current_time\n";
            // echo "Shift start time: $shift_date $shift_time_in\n";
            // echo "Time diff: $time_diff seconds\n";

            // Allow Time In up to 10 minutes before the scheduled time
            if ($time_diff < -600) {  // 600 seconds = 10 minutes
                $error_message = "You cannot time in more than 10 minutes before the shift starts.";
            } elseif ($current_date < $shift_date) {
                $error_message = "The selected shift is in the future. You cannot time in yet.";
            } elseif ($current_date == $shift_date && $time_diff < -600) {
                // This condition is now redundant, as the 10-minute check handles it.
                $error_message = "You cannot time in yet. The shift starts at $shift_time_in.";
            } else {
                // Check if the user has already timed in for this shift
                $check_time_in_query = "SELECT * FROM attendance WHERE id = ? AND date = ? AND shift_type = ? AND Emp_ID = ? AND (status = 'Present' OR status = 'Late')";
                $stmt = $conn->prepare($check_time_in_query);
                $stmt->bind_param("isss", $user_id, $shift_date, $shift_type, $empid);
                $stmt->execute();
                $check_time_in_result = $stmt->get_result();

                if ($check_time_in_result->num_rows > 0) {
                    // User has already timed in for this shift
                    $error_message = "You have already timed in for this shift.";
                } else {
                    // Determine if the user is late or on time
                    $status = ($time_diff > 0) ? 'Late' : 'Present';

                    // Insert Time In record with shift times
                    $time_in_query = "INSERT INTO attendance (id, date, shift_type, time_in, shift_time_in, shift_time_out, status, Emp_ID) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
                    $stmt = $conn->prepare($time_in_query);
                    $stmt->bind_param("issssss", $user_id, $shift_date, $shift_type, $shift_time_in, $shift_time_out, $status, $empid);
                    $stmt->execute();
                    $stmt->close();

                    // Redirect to avoid form resubmission
                    header("Location: dashboard.php?page=Attendance");
                    exit();
                }
                $stmt->close();
            }
        }
    } elseif ($_POST['action'] == 'time_out') {
        // Time Out logic: Update the time_out field
        $time_out_query = "UPDATE attendance SET time_out = NOW() WHERE id = ? AND time_out IS NULL ORDER BY date DESC LIMIT 1";
        $stmt = $conn->prepare($time_out_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Now calculate the worked hours and update the balance
        $query = "SELECT time_in, time_out, salary_rate, balance FROM attendance 
                  JOIN users ON attendance.id = users.id 
                  WHERE attendance.id = ? AND time_out IS NOT NULL ORDER BY date DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $attendance = $result->fetch_assoc();
        $stmt->close();

        // Calculate hours worked
        $time_in = strtotime($attendance['time_in']);
        $time_out = strtotime($attendance['time_out']);
        $worked_seconds = $time_out - $time_in; // Difference in seconds
        $worked_hours = $worked_seconds / 3600; // Convert seconds to hours (this is a decimal value)

        // Get the user's salary rate and current balance
        $salary_rate = $attendance['salary_rate'];
        $current_balance = $attendance['balance'];

        // Calculate total salary based on worked hours (salary_rate * worked_hours)
        $total_salary = $salary_rate * $worked_hours;

        // Add the computed salary to the user's current balance
        $new_balance = $current_balance + $total_salary;

        // Round to 2 decimal places for accuracy
        $new_balance = round($new_balance, 2);

        // Update the balance in the database
        $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
        $stmt = $conn->prepare($update_balance_query);
        $stmt->bind_param("di", $new_balance, $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect after time out processing
        header("Location: dashboard.php?page=Attendance");
        exit();
    }
}


// Check if the user has timed in but not timed out for today
$query = "SELECT * FROM attendance WHERE id = ? AND time_out IS NULL ORDER BY date DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
$timed_in = $attendance_result->num_rows > 0; // True if there's an open Time In without a Time Out
$stmt->close();


// Fetch assigned shifts for the user from the shift_schedule table
$query = "SELECT a.date, a.shift_type, a.shift_start, a.shift_end
FROM shift a
JOIN users b ON a.Emp_ID = b.Emp_ID
WHERE a.Emp_ID = ?
ORDER BY a.date;";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $empid);  // "s" means string for Emp_ID
$stmt->execute();
$result = $stmt->get_result();
$shifts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// Get the page to display (default to index.html content)
$page = isset($_GET['page']) ? $_GET['page'] : 'index';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style1.css?v=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <nav class="navbar">
        <div class="logo">OKX LANG AKO</div>
        <div class="user-info">
            <!-- Use the dynamic profile picture path -->
            <div class="profile-pic"><img src="<?= htmlspecialchars($profile_pic_path); ?>" alt="User"></div>
            <div class="action-buttons">
                <li class="<?= ($page == 'settings') ? 'active' : '' ?>"><a href="dashboard.php?page=settings"><i
                            class="fa-solid fa-gear"></i> Settings</a></li>
                <button type="button" class="logout-btn" onclick="confirmLogout(event)">Logout</button>
            </div>
        </div>
    </nav>

    <!-- Sign Out Confirmation Modal -->


    <div class="profile-banner">
        <div class="profile-info">
            <!-- Use the dynamic profile picture path here as well -->
            <img src="<?= htmlspecialchars($profile_pic_path); ?>" alt="" class="profile-banner-pic">
            <div class="profile-details">
                <h1><?= htmlspecialchars($user['username']); ?></h1>
                <p>Balance: <strong>₱<?= number_format($balance, 2); ?></strong></p>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <ul class="dashboard-nav">
            <li class="<?= ($page == 'index') ? 'active' : '' ?>"><a href="dashboard.php?page=index"
                    class="dashboard">Dashboard</a></li>
            <li class="<?= ($page == 'shift') ? 'active' : '' ?>"><a href="dashboard.php?page=shift"
                    class="shift">Shift</a></li>
            <li class="<?= ($page == 'Attendance') ? 'active' : '' ?>"><a href="dashboard.php?page=Attendance"
                    class="att">Attendance</a></li>
            <li class="<?= ($page == 'payout') ? 'active' : '' ?>"><a href="dashboard.php?page=payout"
                    class="payout">Payout</a></li>
        </ul>
    </div>

    <div class="content-section">
        <?php
        switch ($page) {
            case 'Attendance':
                ?>
                <div class="attendance-container">
                    <h1 class="attendance-heading">Attendance</h1>
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Scheduled Time-in</th>
                                <th>Scheduled Time-out</th>
                                <th>Actual Time-in</th>
                                <th>Actual Time-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $attendance_query = "SELECT date, shift_type, shift_time_in, shift_time_out, time_in, time_out, status FROM attendance WHERE id = ? ORDER BY date";
                            $stmt = $conn->prepare($attendance_query);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $attendance_result = $stmt->get_result();
                            while ($row = $attendance_result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['date']}</td>
                                    <td>{$row['shift_type']}</td>
                                    <td>{$row['shift_time_in']}</td>
                                    <td>{$row['shift_time_out']}</td>
                                    <td>{$row['time_in']}</td>
                                    <td>{$row['time_out']}</td>
                                    <td class='status {$row['status']}'>{$row['status']}</td>
                                </tr>";
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                    <div class="time-action">
                        <?php if (!$timed_in): ?>
                            <!-- Display Time In form if not yet timed in and if shift is today or future -->
                            <form method="POST" action="dashboard.php?page=Attendance">
                                <select id="shiftDropdown" name="shift_id">
                                    <option value="">Select Shift</option>
                                    <?php

                                    foreach ($shifts as $shift) {
                                        $shift_date = $shift['date'];
                                        $shift_type = $shift['shift_type'];
                                        $shift_time_in = $shift['shift_start'];
                                        $shift_time_out = $shift['shift_end'];

                                        $current_date = date("Y-m-d");
                                        $current_time = date("H:i:s");

                                        // Only show shifts in the future or today if the shift end time hasn’t passed
                                        if (
                                            $shift_date > $current_date ||
                                            ($shift_date == $current_date && $current_time < $shift_time_out)
                                        ) {
                                            echo "<option value='{$shift_date}|{$shift_type}|{$shift_time_in}|{$shift_time_out}'>
                                                    {$shift_date} {$shift_type} ({$shift_time_in} - {$shift_time_out})
                                                </option>";
                                        }
                                    }

                                    ?>
                                </select>
                                <input type="hidden" name="action" value="time_in">
                                <button type="submit" id="timeButton">Time In</button>
                            </form>
                        <?php else: ?>
                            <!-- Display Time Out button if already timed in -->
                            <form method="POST" action="dashboard.php?page=Attendance">
                                <input type="hidden" name="action" value="time_out">
                                <button type="submit" id="timeButton">Time Out</button>
                            </form>
                        <?php endif; ?>
                        <?php if (!empty($error_message)): ?>
                            <p class="error-message" style="color: red;"><?= htmlspecialchars($error_message); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                break;

            case 'shift':
                ?>
                <div class="shift-container">
                    <h1 class="shift-heading">Shift Schedule</h1>
                    <table class="shift-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift Type</th>
                                <th>Time In</th>
                                <th>Time Out</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($shifts)) {
                                echo "<tr><td colspan='4'>No shifts found.</td></tr>";  // Display message if no shifts
                            } else {
                                // Loop through each shift and display it
                                foreach ($shifts as $shift) {
                                    // Output each shift's details in a table row
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($shift['date']) . "</td>";
                                    echo "<td>" . htmlspecialchars($shift['shift_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($shift['shift_start']) . "</td>";
                                    echo "<td>" . htmlspecialchars($shift['shift_end']) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                break;

            default:
                ?>
                <div class="content-widgets">
                    <div class="earnings-chart">
                        <h1>Earnings</h1>
                        <div id="curve_chart" style="width: 100%; height: 450px;"></div>
                    </div>
                    <div class="sidebar-cards">
                        <div class="sidebar-card total-hours">
                            <h2>Total Hours</h2>
                            <h3><?= $total_hours; ?></h3>
                            <p>in past 1 month</p>
                        </div>
                        <div class="sidebar-card total-salary">
                            <h2>Total Salary</h2>
                            <h3>₱<?= number_format($balance, 2); ?></h3>
                            <p>in past 1 month</p>
                        </div>
                        <div class="sidebar-card shift">
                            <h2>Shift</h2>
                            <h3><?= $total_shifts; ?></h3>
                            <p>in past 1 month</p>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'payout':
                ?>
                <?php
                // Check if today is the 15th or 30th of the month
                $current_day = date('j'); // Get the day of the month
        
                if ($current_day == 29 || $current_day == 30) {
                    // Display the payout form if it's the 15th or 30th
                    ?>
                    <div class="content-widgets">
                        <div class="earnings-chart">
                            <h1>Earnings Chart</h1>
                            <div id="curve_chart" style="width: 1400px; height: 450px"></div>
                        </div>
                        <div class="tatlong-card">
                            <div class="total-hours">
                                <h2>Total Salary</h2>
                                <h3>₱<?= number_format($balance, 2); ?></h3>
                                <p>in past 1 month</p>
                            </div>
                            <button class="withdraw-btn" id="withdrawBtn">Withdraw</button>
                            <!-- Modal Structure -->
                            <div id="payoutModal" class="modal" style="display: none;">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <h2>Payout Request</h2>
                                    <p>Total Salary Available: ₱<?= number_format($balance, 2); ?></p>

                                    <form action="process_payout.php" method="POST">
                                        <label for="amount">Amount to Withdraw:</label>
                                        <input type="number" id="amount" name="amount" placeholder="Enter amount" required min="0"
                                            step="0.01">
                                        <label for="bank">Payment Method:</label>
                                        <select id="bank" name="bank">
                                            <option value="cash">Cash</option>
                                            <option value="e_wallet">E-wallet</option>
                                        </select>
                                        <button type="submit" class="submit-btn">Confirm Withdraw</button>
                                        <?php
                                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                            $amount = $_POST['amount'];
                                            $bank = $_POST['bank'];

                                            // Process the payout request using the amount and selected bank
                                            // Make sure to validate the data, especially checking that the amount is less than or equal to the total salary
                                            echo "Amount to Withdraw: ₱" . number_format($amount, 2);
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    // Show message if it's not the 15th or 30th
                    echo "<p>Payouts are only available on the 15th and 30th of each month. Please try again on those dates.</p>";
                }
                ?>


                <?php
                break;

            case 'settings':
                ?>
                <div class="settings-container">
                    <h1>Settings</h1>
                    <form method="POST" action="dashboard.php?page=settings" enctype="multipart/form-data">
                        <label for="profile-pic">Profile Picture:</label>
                        <input type="file" name="profile-pic" accept="image/*">

                        <label for="first-name">First Name:</label>
                        <input type="text" name="first-name" value="<?= htmlspecialchars($user['first_name']); ?>">

                        <label for="last-name">Last Name:</label>
                        <input type="text" name="last-name" value="<?= htmlspecialchars($user['last_name']); ?>">

                        <label for="username">Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>">

                        <label for="dob">Date of Birth:</label>
                        <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']); ?>">

                        <label for="gender">Gender:</label>
                        <select name="gender">
                            <option value="male" <?= $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                        </select>

                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>">

                        <label for="address">Address:</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address']); ?>">

                        <label for="password">New Password:</label>
                        <input type="password" name="password" placeholder="Enter new password">

                        <label for="confirm-password">Confirm Password:</label>
                        <input type="password" name="confirm-password" placeholder="Confirm new password">

                        <button type="submit" name="save-settings">Save Changes</button>

                        <?php if (!empty($error_message)): ?>
                            <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_GET['success'])): ?>
                            <p style="color: green;">Profile updated successfully!</p>
                        <?php endif; ?>
                    </form>
                </div>
                <?php
                break;
        }
        ?>
    </div>
    <script src="script1.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Prevent the default link action
            swal({
                title: "Are you sure?",
                text: "You will be logged out from your account.",
                icon: "warning",
                buttons: ["Cancel", "Logout"],
                dangerMode: true,
            }).then((willLogout) => {
                if (willLogout) {
                    // Redirect to the logout page
                    window.location.href = "login.php";
                } else {
                    swal("Logout cancelled");
                }
            });
        }
    </script>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile has been successfully updated.',
                confirmButtonText: 'Okay'
            });
        </script>
    <?php endif; ?>
</body>

</html>