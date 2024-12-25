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
		<a href="admindashboard.php" class="brand">
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
						<li><a class="active" href="#">Add Departments</a></li>
					</ul>
				</div>
			</div>

			<div class="table-department">

				<form action="">
					<label for="department-id">Department ID</label>
					<input type="text" id="department-id" name="departmentid" required>

					<label for="department-name">Department Name</label>
					<input type="text" id="department-name" name="departmentname" required>

					<label for="department-head">Department Head</label>
					<input type="text" id="department-head" name="departmenthead" required>



					<div class="button-container">
					<a href="department.php" class="btn submit-btn" style="background-color: red; border-color: red;">Back</a>


					</div>
					<form method="POST">
						<input type="hidden" name="add-department">
						<button type="submit" name="add-department" class="add-department-btn">ADD DEPARTMENT</button>
					</form>
				</form>
				
                <?php
            include('connection.php');

            if (isset($_REQUEST['add-department'])) {
                // Collect form inputs using POST method
                $deptid = $_REQUEST['departmentid'] ?? '';
                $deptname = $_REQUEST['departmentname'] ?? '';
                $depthead = $_REQUEST['departmenthead'] ?? '';

                // Check if values are being received correctly
                if (empty($deptid) || empty($deptname) || empty($depthead)) {
                    echo "Error: All fields are required!";
                } else {
                    // Debugging: Display values to check if inputs are being captured


                    // Insert query to the department table
                    $insert_query = mysqli_query($connection, "INSERT INTO department (dept_ID, dept_name, dept_head) VALUES ('$deptid', '$deptname', '$depthead')");

                    if ($insert_query) {
                        $msg = "Department successfully added!";
                    } else {
                        $msg = "Error in adding the department!";
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