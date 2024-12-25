<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session

header("Location: index.php"); // Redirect back to the login page
exit();
