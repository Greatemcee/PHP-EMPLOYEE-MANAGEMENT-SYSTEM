<?php
include('connection.php');
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style (3).css">

	<title>EmployeeHub - Manage Departments</title>
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
			<a href="#" class="profile">
				<img src="img/SAM_0103.JPG">
			</a>
		</nav>

		<main>
			<div class="head-title">
				<div class="left">
					<h1>Departments</h1>
					<ul class="breadcrumb">
						<li><a href="#">Departments</a></li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li><a class="active" href="#">Add Shift</a></li>
					</ul>
				</div>
			</div>

			<div class="table-department">

				<form action="">
					<label for="department-id">Shift Date</label>
					<input type="date" id="department-id" name="shift-date" required>

					<label for="department-name">Shift Start</label>
					<input type="time" id="department-name" name="shift-start" required>

					<label for="department-head">Shift End</label>
					<input type="time" id="department-head" name="shift-end" required>

					<label for="department-head">Shift Type</label>
					<input type="text" id="department-head" name="shift-type" required>




					<div class="button-container">
						<a href="shift-schedule.php" class="btn submit-btn"
							style="background-color: red; border-color: red;">Back</a>


					</div>
					<form action="">
						<input type="hidden" name="add-shift">
						<button type="submit" name="add-shift" class="add-department-btn">ADD SHIFT</button>
					</form>
				</form>
				<?php
				include('connection.php');

				if (isset($_REQUEST['add-shift'])) {
					$shiftid = rand(1000, 9999);
					$shift_id = 'SHFT-' . $shiftid;

					$shiftdate = $_REQUEST['shift-date'];
					$shiftstart = $_REQUEST['shift-start'];
					$shiftend = $_REQUEST['shift-end'];
					$shifttype = $_REQUEST['shift-type'];

					if (empty($shiftdate) || empty($shiftstart) || empty($shiftend)) {
						echo "Error: All fields are required!";
					} else {
						// Prepare the SQL query
						$insert_query = mysqli_query($connection, "INSERT INTO shift (shiftID, date, shift_start, shift_end, shift_type) VALUES ('$shift_id', '$shiftdate', '$shiftstart', '$shiftend', '$shifttype')");

						// Check if the query was successful
						if ($insert_query) {
							$msg = "Shift successfully added!";
						} else {
							// Output the error message
							$msg = "Error in adding the shift: " . mysqli_error($connection);
						}
					}
				}
				?>
			</div>
		</main>
	</section>
	<script src="script (1).js"></script>
</body>
<?php include('footer.php') ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<<script type="text/javascript">
	<?php
	if (isset($msg)) {
		echo 'swal("' . $msg . '");';
	}
	?>
	</script>

</html>