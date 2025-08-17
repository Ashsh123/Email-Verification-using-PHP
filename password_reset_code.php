<?php
session_start();
include('dbcon.php');

// PHPMailer classes ko include karein
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/PHPMailer.php';
function send_password_reset($get_name, $get_email, $token)
{
    $mail = new PHPMailer(true);
    //  $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->isSMTP();
    $mail->SMTPAuth   = true;

    // SMTP configuration
    $mail->Host       = 'smtp.gmail.com';
    $mail->Username   = 'your-email@gmail.com';
    $mail->Password   = 'your-app-password';

    // SMTP security and port
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Sender and recipient
    $mail->setFrom('your-email@gmail.com', $get_name);
    $mail->addAddress($get_email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Resend - Email Verification';
    $mail->Body = '
            <h2>Alert!!!</h2>
            <h3>You are receiving this email because we recieved a password reset request for your account.</h3>
            <br/><br/>
            <a href="http://localhost/ashish/Email_Verification/password_change.php?token=' . $token . '&&email=' . $get_email . '">Click me</a>
            <p>If you do not do that, please ignore this email.</p>
            <p>Best regards,<br>Password Reset Team</p>
        ';

    // Send the email
    $mail->send();
}


if (isset($_POST['password_reset_link'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $token = md5(rand());
    $check_email = "SELECT email FROM user_details WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($con, $check_email);

    if (mysqli_num_rows($check_email_run) > 0) {
        $row = mysqli_fetch_array($check_email_run);
        $get_name = $row['name'];
        $get_email = $row['email'];

        $update_token = "UPDATE user_details SET verify_token='$token' WHERE email='$get_email' LIMIT 1";
        $update_token_run = mysqli_query($con, $update_token);

        if ($update_token_run) {
            send_password_reset($get_name, $get_email, $token);
            $_SESSION['status'] = "We e-mailed you a password reset link.";
            header("Location: password_reset.php");
            exit(0);
        } else {

            $_SESSION['status'] = "Something went wrong!!! #1";
            header("Location: password_reset.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "No email found";
        header("Location: password_reset.php");
        exit(0);
    }
}



if (isset($_POST['password_update'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    $token = mysqli_real_escape_string($con, $_POST['password_token']);

    if (!empty($token)) {

        if (!empty($email) && !empty($new_password) && !empty($confirm_password)) {
            //checking token is valid or not
            $check_token = "SELECT verify_token FROM user_details WHERE verify_token='$token' LIMIT 1";
            $check_token_run = mysqli_query($con, $check_token);
            if (mysqli_num_rows($check_token_run) > 0) {
                if ($new_password == $confirm_password) {
                    $update_password = "UPDATE user_details SET password='$new_password' WHERE verify_token='$token' LIMIT 1";
                    $update_password_run = mysqli_query($con, $update_password);
                    if ($update_password_run) {
                        $new_token = md5(rand()) . "ashish";
                        $update_token = "UPDATE user_details SET verify_token='$new_token' WHERE verify_token='$token' LIMIT 1";
                        $update_token_run = mysqli_query($con, $update_token);
                        $_SESSION['status'] = "Congratulations, your new password successfullly updated!!!";
                        header("Location: login.php");
                        exit(0);
                    } else {
                        $_SESSION['status'] = "Did not update password. Something went worng.!";
                        header("Location: password_change.php?token=$token&email=$email");
                        exit(0);
                    }
                } else {
                    $_SESSION['status'] = "Password and confirm password does not match.";
                    header("Location: password_change.php?token=$token&email=$email");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = "Invalid Token";
                header("Location: password_change.php?token=$token&email=$email");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "All fields are mandatory";
            header("Location: password_change.php?token=$token&email=$email");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "No Token Available";
        header("Location: password_change.php");
        exit(0);
    }
}
