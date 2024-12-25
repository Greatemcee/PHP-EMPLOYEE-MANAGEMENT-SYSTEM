<?php
session_start();
echo "<link rel='stylesheet' href='css/style2.css' type='text/css'>";


include('connection.php');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">


    <title>EmployeeHub</title>
</head>

<body>



    <section id="sidebar">
        <a href="dashboard.html" class="brand">
            <i class='bx bxs-id-card'></i>
            <span class="text">EmployeeHub</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="admindashboard.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="employee.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Employees</span>
                </a>
            </li>
            <li class="active">
                <a href="applicants.php">
                    <i class='bx bx-user'></i>
                    <span class="text">Applicants</span>
                </a>
            </li>
            <li>
                <a href="department.php">
                    <i class='bx bx-buildings'></i>
                    <span class="text">Departments</span>
                </a>
            </li>
            <li>
                <a href="atendance.php">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Reports</span>
                </a>
            </li>
            <li>
                <a href="shift-schedule.php">
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">Shift Schedule</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="logout.html" class="logout" onclick="confirmLogout(event)">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Applicants</a>


            <a href="#" class="profile">
                <img src="img/SAM_0103.JPG">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Applicants List</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Applicants</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Manage Applicants</a></li>
                    </ul>
                </div>
            </div>


            <!-- Attendance List -->
            <div class="table-employees">
                <div class="employee-attendance">
                    <div class="head">
                        <h3>Applicants Records</h3>

                    </div>
                    <table class="datatable table table-stripped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee Name</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Created At</th>
                                <th>Apply</th>

                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">

                            <?php
                            $get_query = mysqli_query($connection, "select * from applicants");



                            while ($set = mysqli_fetch_array($get_query)) {
                            ?>
                                <tr>
                                    <td><?php echo $id = $set['id']; ?></td>
                                    <td><?php echo $set['Fname'] . " " . $set['Lname']; ?></td>
                                    <td><?php echo $set['username']; ?></td>
                                    <td><?php echo $set['email']; ?></td>
                                    <td><?php echo $set['gender']; ?></td>
                                    <td><?php echo $set['dob']; ?></td>
                                    <td><?php echo $set['created_at']; ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="applicant_id" value="<?php echo $set['id']; ?>">
                                            <button type="submit" name="Accept" value="<?php echo $set['id']; ?>" class="btn btn-primary submit-btn">Accept</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <?php
                        include('connection.php');

                        // Check if the Accept button was clicked and applicant_id is set
                        if (isset($_POST['Accept']) && isset($_POST['applicant_id'])) {
                            $id = $_POST['applicant_id'];  // Get the applicant ID

                            // Fetch the applicant data based on the ID
                            $get_query = mysqli_query($connection, "SELECT * FROM applicants WHERE id='$id'");
                            $set = mysqli_fetch_array($get_query);


                            if ($set) {
                                // Store the applicant data into variables
                                $fname = $set['Fname'];
                                $lname = $set['Lname'];
                                $username = $set['username'];
                                $email = $set['email'];
                                $gender = $set['gender'];
                                $dob = $set['dob'];
                                $pass = $set['password'];
                                $address = $set['address'];
                                $role = "Employee";

                                // Generate a unique employee ID (you can modify this)
                                $empid = rand(1000, 9999);  // Example ID generation
                                $employee_id = 'EMP-' . $empid;

                                // Insert the applicant data into the employee table
                                $insert_query = mysqli_query($connection, "INSERT INTO users (first_name, last_name, username, address, email, gender, dob, password, Emp_ID, role) VALUES ('$fname', '$lname', '$username', '$address', '$email', '$gender', '$dob', '$pass', '$employee_id', '$role')");

                                if (!$insert_query) {
                                    die("Error in INSERT query: " . mysqli_error($connection));
                                }
                                if ($insert_query) {
                                    // Successfully inserted, delete the applicant record
                                    $delete_query = mysqli_query($connection, "DELETE FROM applicants WHERE id='$id'");
                                    if ($delete_query) {
                                        $msg = "Employee Accepted and moved to Employee List";
                                    } else {
                                        $msg = "Error in deleting applicant record!";
                                    }
                                } else {
                                    $msg = "Error in accepting the employee!";
                                }

                                echo $msg;
                            } else {
                                echo "Applicant not found!";
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </main>
    </section>

</body>
<?php include('footer.php') ?>
<script type="text/javascript">
    <?php
    if (isset($msg)) {

        echo 'swal("' . $msg . '");';
    }
    ?>
</script>
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
                window.location.href = "../login.php";
            } else {
                swal("Logout cancelled");
            }
        });
    }
</script>

</html>