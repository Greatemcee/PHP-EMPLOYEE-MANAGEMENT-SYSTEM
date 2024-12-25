<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style (3).css">
    <title>Employee Reports</title>
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
            <li>
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
            <li class="active">
                <a href="reports.html">
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
            <a href="#" class="nav-link">Reports</a>

            <a href="#" class="profile">
                <img src="img/SAM_0103.JPG">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Employee Reports</h1>

                </div>
            </div>
            <form action="" method="POST">
                <div class="filter-container">
                    <label for="from-date">From</label>
                    <input type="date" id="from-date" name="from-date">

                    <label for="to-date">To</label>
                    <input type="date" id="to-date" name="to-date">

                    <select id="department" name="department">
                        <option value="">Select Department</option>
                        <?php
                        include('connection.php');
                        $get_query = mysqli_query($connection, 'select * from department');
                        while ($set = mysqli_fetch_array($get_query)) {
                            ?>
                            <option value="<?php echo $set['dept_name']; ?>">
                                <?php echo $set['dept_ID'] . ' - ' . $set['dept_name']; ?></option>
                        <?php } ?>
                    </select>

                    <button type="submit" name="serach-btn" class="edit-btn">Search</button>
                </div>

            </form>
            </div>
            <div class="table-reports">
                <div class="order">
                    <div class="head">
                        <h3>Employee Reports</h3>

                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Shift Type</th>
                                <th>Date</th>
                                <th>Time in</th>
                                <th>Time out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <?php
                            include('connection.php');
                            // Check if the search button is clicked
                            if (isset($_REQUEST['serach-btn'])) {
                                $fromdate = $_POST['from-date'];
                                $todate = $_POST['to-date'];
                                $department = $_POST['department'];

                                // Ensure the date format is correct
                                $fromdate = date('Y-m-d', strtotime($fromdate));
                                $todate = date('Y-m-d', strtotime($todate));

                                // Prepare the query
                                $search_query = mysqli_query($connection, "SELECT * 
                                                FROM users a 
                                                INNER JOIN attendance b ON b.Emp_ID = a.Emp_ID 
                                                WHERE a.Department = '$department' 
                                                AND DATE(b.Date) BETWEEN '$fromdate' AND '$todate'");

                                // Check if the query was successful
                                if (!$search_query) {
                                    // If the query failed, show an error
                                    die("Error executing query: " . mysqli_error($connection));
                                }

                                // Fetch the results and display them
                                while ($set = mysqli_fetch_array($search_query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $set['first_name'] . " " . $set['last_name']; ?></td>
                                        <td><?php echo $set['department']; ?></td>
                                        <td><?php echo $set['Position']; ?></td>
                                        <td><?php echo $set['shift_type']; ?></td>
                                        <td><?php echo $set['date']; ?></td>
                                        <td><?php echo $set['shift_time_in']; ?></td>
                                        <td><?php echo $set['shift_time_out']; ?></td>
                                        <td><?php echo $set['status']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                // Query for when no search is applied
                                $get_query = mysqli_query($connection, "SELECT * 
                                            FROM attendance b 
                                            INNER JOIN users a ON b.Emp_ID = a.Emp_ID 
                                            WHERE a.Emp_ID = b.Emp_ID");

                                // Check if the query was successful
                                if (!$get_query) {
                                    // If the query failed, show an error
                                    die("Error executing query: " . mysqli_error($connection));
                                }

                                // Fetch the results and display them
                                while ($set = mysqli_fetch_array($get_query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $set['first_name'] . " " . $set['last_name']; ?></td>
                                        <td><?php echo $set['department']; ?></td>
                                        <td><?php echo $set['Position']; ?></td>
                                        <td><?php echo $set['shift_type']; ?></td>
                                        <td><?php echo $set['date']; ?></td>
                                        <td><?php echo $set['shift_time_in']; ?></td>
                                        <td><?php echo $set['shift_time_out']; ?></td>
                                        <td><?php echo $set['status']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>
    
</body>
<script src="script.js"></script>
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