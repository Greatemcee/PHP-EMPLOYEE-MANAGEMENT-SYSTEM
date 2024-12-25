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
		<a href="#" class="brand">
			<i class='bx bxs-id-card'></i>
			<span class="text">EmployeeHub</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="dashboard.html">
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
				<a href="#" class="logout" onclick="confirmLogout(event)">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>

	</section>

	<section id="content">
		<nav>
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Dashboard</a>

			<a href="#" class="profile">
				<img src="img/SAM_0103.JPG">
			</a>
		</nav>

		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<ul class="breadcrumb">
						<li><a href="#">Dashboard</a></li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li><a class="active" href="#">Home</a></li>
					</ul>
				</div>
			</div>

			<ul class="box-info">
				<?php
				include('connection.php');

				$get_query = mysqli_query($connection, "select count(*) from users");
				$employee = mysqli_fetch_row($get_query);
				?>
				<li>
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo $employee[0]; ?></h3>
						<p>Active Employees</p>
					</span>
				</li>
				<li>
					<?php
					include('connection.php');

					$get_query = mysqli_query($connection, "select count(*) from department");
					$department = mysqli_fetch_row($get_query);
					?>
					<i class='bx bx-buildings'></i>
					<span class="text">
						<h3><?php echo $department[0]; ?></h3>
						<p>Active Departments</p>
					</span>
				</li>
				<li>
					<?php
					include('connection.php');

					$get_query = mysqli_query($connection, "select count(*) from shift");
					$shift = mysqli_fetch_row($get_query);
					?>
					<i class='bx bx-time'></i>
					<span class="text">
						<h3><?php echo $shift[0]; ?></h3>
						<p>Total Shifts</p>
					</span>
				</li>
			</ul>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Employee Activity</h3>
						<i class='bx bx-filter'></i>
					</div>


					<table>
						<thead>
							<tr>
								<th>Employee</th>
								<th>Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							include('connection.php');
							$get_query = mysqli_query($connection, "select * from users");

							while ($set = mysqli_fetch_array($get_query)) {
								?>
								<tr>
									<td>
										<p><?php echo $set['first_name'] . " " . $set['last_name']; ?></p>
									</td>
									<td><?php echo $set['Joining_date']; ?></td>
									<td><span class="status completed"><?php echo $set['Status']; ?></span></td>
								</tr>

							</tbody>
						<?php } ?>
					</table>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Task Management</h3>
						<i class='bx bx-plus'></i>
						<i class='bx bx-filter'></i>
					</div>
					<ul class="todo-list">
						<li class="completed">
							<p>Prepare Payroll</p><i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="completed">
							<p>Employee Onboarding</p><i class='bx bx-dots-vertical-rounded'></i>
						</li>
						<li class="not-completed">
							<p>Monthly Reports</p><i class='bx bx-dots-vertical-rounded'></i>
						</li>
					</ul>
				</div>
			</div>
		</main>
	</section>


	<script src="script.js"></script>
</body>
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
                window.location.href = "../login.php";
            } else {
                swal("Logout cancelled");
            }
        });
    }
</script>


</html>