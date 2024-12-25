<?php include('connection.php'); ?>

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
		<a href="admindashboard.php" class="brand">
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
			<li class="active">
				<a href="employees.html">
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
			<a href="#" class="nav-link">Employees</a>

			<a href="#" class="profile">
				<img src="img/SAM_0103.JPG">
			</a>
		</nav>

		<main>
			<div class="head-title">
				<div class="left">
					<h1>Manage Employees</h1>
					<ul class="breadcrumb">
						<li><a href="#">Employees</a></li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li><a class="active" href="#">Manage Employees</a></li>
					</ul>
				</div>
			</div>

			<div class="employee-actions">

			</div>
			<div class="table-employees">
				<div class="employee-list">
					<div class="head">
						<h3>Employee List</h3>
					</div>
					<table>
						<thead>
							<tr>
								<th>ID</th>
								<th>Employee Name</th>
								<th>Employee ID</th>
								<th>Gender</th>
								<th>Email</th>
								<th>Department</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody id="employeeTableBody">
							<?php
							include('connection.php');

                            if (isset($_POST['delete-emp']) && isset($_POST['dlt-emp'])) {
                                $getid = $_POST['dlt-emp'];


                                $dlt_query = mysqli_query($connection, "delete from users where id = '$getid'");
								$msg = $dlt_query ? "Successfully Deleted!" : "Error";
                                
                            }

							$get_query = mysqli_query($connection, "select * from users");
							while ($set = mysqli_fetch_array($get_query)) {
							?>
								<tr>
									<td><?php echo $set['id']; ?></td>
									<td><?php echo $set['first_name'] . " " . $set['last_name']; ?></td>
									<td><?php echo $set['Emp_ID']; ?></td>
									<td><?php echo $set['gender']; ?></td>
									<td><?php echo $set['email']; ?></td>
									<td><?php echo $set['department']; ?></td>
									<td><?php echo $set['Status']; ?></td>
									<td>
										<form action="manage-employee.php" method="POST">
											<input type="hidden" name="manage" value="<?php echo $set['Emp_ID'];?>">
											<button type="submit" name="Accepct" value="<?php echo $set['Emp_ID'];?>" class="edit-btn">Manage</button>
										</form>
										<form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="dlt-emp" value="<?php echo $set['id']; ?>">
                                            <button type="button" class="delete-btn" onclick="confirmDelete(this)">Delete</button>
                                        </form>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>


			<script src="script.js"></script>
</body>
<?php include('footer.php') ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        function confirmDelete(button) {
            swal({
                title: "Are you sure?",
                text: "Once deleted, this employee cannot be restored!",
                icon: "Warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    
                    let form = button.closest('form');
                    let deleteInput = document.createElement('input');
                    deleteInput.type = "hidden";
                    deleteInput.name = "delete-emp";
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