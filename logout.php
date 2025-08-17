<?php
session_start();
unset($_SESSION['authentication']);
unset($_SESSION['auth_user']);
$_SESSION['status'] = "You have logged out successfully.";
header("Location: login.php");
?>



