<?php
include('dbcon.php');
session_start();

if (isset($_POST['login_new_btn'])) {
    if (!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {
        $email = mysqli_real_escape_string($con, trim($_POST['email']));
        $password = mysqli_real_escape_string($con, trim($_POST['password']));

        $login_query = "SELECT * FROM user_details WHERE email='$email' AND password='$password' LIMIT 1";
        $login_query_run = mysqli_query($con, $login_query);
        if (mysqli_num_rows($login_query_run) > 0) {
            $row = mysqli_fetch_array($login_query_run);

            if ($row['verify_status'] == '1') {
               $_SESSION['authentication'] = TRUE;
               $_SESSION['auth_user'] = [
                'username' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
               ];

                $_SESSION['status'] = "Login successful!";
                header("Location: dashboard.php");
                exit(0);
            } else {
                $_SESSION['status'] = "Please verify your email address before logging in.";
                header("Location: login.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Invalid email or password.";
            header("Location: login.php");
            exit(0);
        }
    } else {

        $_SESSION['status'] = "All fields are mandatory.";
        header("Location: login.php");
        exit(0);
    }
}
