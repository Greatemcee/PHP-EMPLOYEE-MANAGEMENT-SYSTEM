<?php
include('connection.php');

// Handle form submission before any HTML output
if (isset($_POST['submited'])) {
    $getid = $_POST['emp_id'];
    $fname = $_POST['first-name'];
    $lname = $_POST['lastname-display'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $joindate = $_POST['joining-id'];
    $empid = $_POST['employee-id'];
    $dob = $_POST['dob'];
    $dept = $_POST['department'];
    $status = $_POST['status'];
    $position = $_POST['Position'];
    $role = "Employee";

    // Ensure the selected shift is not the default placeholder

       

        // Update the employee record
        $update_query = mysqli_query($connection, "UPDATE users SET first_name='$fname', last_name = '$lname', username='$username', email='$email', password='$pass', department='$dept', Joining_date='$joindate', Status='$status', Position = '$position', role = '$role' WHERE Emp_ID='$getid'");

        if ($update_query) {
            $msg = "Employee information updated successfully.";
        } else {
            $msg = "Error updating record: " . mysqli_error($connection);
        }
}

// Fetch employee details if 'manage' is set
$employee_found = false;
if (isset($_POST['manage'])) {
    $getid = $_POST['manage'];
    $get_query = mysqli_query($connection, "SELECT * FROM users WHERE Emp_ID = '$getid'");

    if ($get_query && mysqli_num_rows($get_query) > 0) {
        $set = mysqli_fetch_array($get_query);
        $employee_found = true;
    } else {
        $msg = "Employee not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style (3).css">
    <title>Add Employee</title>
</head>

<body>
    <section id="sidebar">
        <a href="dashboard.html" class="brand">
            <i class='bx bxs-id-card'></i>
            <span class="text">EmployeeHub</span>
        </a>
        
        
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="profile"><img src="img/SAM_0103.JPG"></a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Employee Registration Form</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Employee</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Employee Registration</a></li>
                    </ul>
                </div>
            </div>

            <div class="add-emp">
                <a href="employee.php" class="back-button" style="background-color: red; border-color: red;">Back</a>
                <?php if ($employee_found): ?>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="employee-form">
                        <input type="hidden" name="emp_id" value="<?php echo $getid; ?>">

                        <div class="form-group">
                            <label class="form-label" for="first-name">First Name</label>
                            <input type="text" id="first-name" value="<?php echo $set['first_name'] ?>" name="first-name-display" disabled>
                            <input type="hidden" name="first-name" value="<?php echo $set['first_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="last-name">Last Name</label>
                            <input type="text" id="last-name" value="<?php echo $set['last_name']; ?>" name="lastname-display">
                            <input type="hidden" name="last-name" value="<?php echo $set['last_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" id="username" value="<?php echo $set['username']; ?>" name="username-display" disabled>
                            <input type="hidden" name="username" value="<?php echo $set['username']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" value="<?php echo $set['email']; ?>" name="email-display" disabled>
                            <input type="hidden" name="email" value="<?php echo $set['email']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" value="<?php echo $set['password']; ?>" name="password-display" disabled>
                            <input type="hidden" name="password" value="<?php echo $set['password']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="joining-id">Joining ID</label>
                            <input type="date" id="joining-id" value="<?php echo date('Y-m-d'); ?>" name="joining-id-display" disabled>
                            <input type="hidden" name="joining-id" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="employee-id">Employee ID</label>
                            <input type="text" id="employee-id" value="<?php echo $set['Emp_ID']; ?>" name="employee-id-display" disabled>
                            <input type="hidden" name="employee-id" value="<?php echo $set['Emp_ID']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="dob">Date of Birth</label>
                            <input type="date" id="dob" value="<?php echo $set['dob']; ?>" name="dob-display" disabled>
                            <input type="hidden" name="dob" value="<?php echo $set['dob']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="position">Position</label>
                            <input type="text" id="last-name" value="<?php echo $set['Position']; ?>" name="Position" required>
                            <input type="hidden" name="last-name">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="<?php echo $set['Status'] ?>"><?php echo $set['Status'] ?></option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="On Leave">On Leave</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="department">Department</label>
                            <select id="department" name="department" required>
                                <option value="<?php echo $set['department']; ?>"><?php echo $set['department']; ?></option>
                                <?php
                                $dept_query = mysqli_query($connection, "select dept_name from department");
                                while ($dept = mysqli_fetch_array($dept_query)) {
                                    echo "<option value='" . $dept['dept_name'] . "'>" . $dept['dept_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        

                        <button class="form-button" name="submited" type="submit">Submit</button>
                    </form>
                <?php else: ?>
                    <p>Employee Manage Successfully</p>
                <?php endif; ?>
            </div>
        </main>
    </section>

    <script src="script.js"></script>
    <?php include('footer.php'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        <?php if (isset($msg)) {
            echo 'swal("' . $msg . '");';
        } ?>
    </script>
</body>

</html>