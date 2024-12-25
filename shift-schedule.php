<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style2.css">

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
            <li>
                <a href="applicants.php">
                    <i class='bx bx-user'></i>
                    <span class="text">Applicants</span>
                </a>
            </li>
            <li>
                <a href="department.php">
                    <i class='bx bx-buildings'></i>
                    <span class="text">Department</span>
                </a>
            </li>
            <li>
                <a href="atendance.php">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Reports</span>
                </a>
            </li>
            <li class="active">
                <a href="shift-schedule.php">
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">Shift Schedule</span>
                </a>
            </li>

            <ul class="side-menu">

                <li>
                    <a href="logout.html" class="logout" onclick="confirmLogout(event)">
                        <i class='bx bxs-log-out-circle'></i>
                        <span class="text">Logout</span>
                    </a>
                </li>
            </ul>
        </ul>

    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="profile">
                <img src="img/SAM_0103.JPG">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Employee Shift Schedule</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Shift Schedule</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Manage Shifts</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-shift">
                <div class="employee-shift">
                    <div class="head">
                        <h3>Shift Time Schedule</h3>
                        <th style="text-align: right;">
                            <form action="addshift-schedule.php" method="POST">
                                <button id="addShiftBtn">Add Shift</button>
                            </form>
                        </th>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Shift Schedule ID</th>
                                <th>Date</th>
                                <th>Shift Start</th>
                                <th>Shift End</th>
                                <th>Employee ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="shiftTableBody">
                            <?php
                            include('connection.php');

                            
                            if (isset($_POST['delete-shifts']) && isset($_POST['delete_shift'])) {
                                $getid = $_POST['delete_shift'];
                                $dlt_query = mysqli_query($connection, "delete from shift where id = '$getid'");
                                $msg = $dlt_query ? "Successfully Deleted!" : "Error";
                            }

                            $get_query = mysqli_query($connection, "select * from shift");

                            while ($set = mysqli_fetch_array($get_query)) {
                            ?>
                                <tr>
                                    <td><?php echo $id = $set['id']; ?></td>
                                    <td><?php echo $set['shiftID']; ?></td>
                                    <td><?php echo $set['date']; ?></td>
                                    <td><?php echo $set['shift_start']; ?></td>
                                    <td><?php echo $set['shift_end']; ?></td>
                                    <td><?php echo $set['Emp_ID']; ?></td>
                                    <td>
                                        <form action="edit-shift.php" method="POST">
                                            <input type="hidden" name="shift_id" value="<?php echo $set['id']; ?>">
                                            <button class="edit-btn" name="edit-shift" value="<?php echo $set['id']; ?>">Edit</button>
                                        </form>
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="delete_shift" value="<?php echo $set['id']; ?>">
                                            <button type="button" class="delete-btn" onclick="confirmDelete(this)">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <?php

                        ?>
                    </table>
                </div>
            </div>
        </main>
    </section>


    <script src="script.js"></script>
</body>
<?php include('footer.php') ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        function confirmDelete(button) {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this shift!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    
                    let form = button.closest('form');
                    let deleteInput = document.createElement('input');
                    deleteInput.type = "hidden";
                    deleteInput.name = "delete-shifts";
                    deleteInput.value = "true";
                    form.appendChild(deleteInput);

                   
                    form.submit();
                } else {
                    swal("Cancelled");
                }
            });
        }

        <?php if (isset($msg)) { echo 'swal("' . $msg . '");'; } ?>
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