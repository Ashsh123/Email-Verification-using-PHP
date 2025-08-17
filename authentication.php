<?php
session_start();

if(!isset($_SESSION['authentication'])) {
    $_SESSION['status'] = "Please login to Access User Dashboard!!!";
    header("Location: login.php");
    exit(0);
}   

?>