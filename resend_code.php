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
function resend_email_verify($name, $email, $verify_token)
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
    $mail->setFrom('a9465490@gmail.com', 'Ashish Rathor');
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Resend - Email Verification';
    $mail->Body = '
            <h2>Welcome to Our Website, ' . $name . '!</h2>
            <p>Thank you for registering. Please click the link below to verify your email address:</p>
            <a href="http://localhost/ashish/Email_Verification/verify_email.php?token=' . $verify_token . '">Verify Email</a>
            <p>If you did not register, please ignore this email.</p>
            <p>Best regards,<br>Email Verification Team</p>
        ';

    // Send the email
    $mail->send();
}


if (isset($_POST['resend_email_verify_btn'])) {
    if (!empty(trim($_POST['email']))) {

        $email = mysqli_real_escape_string($con, $_POST['email']);

        $checkemail_query = "SELECT * FROM user_details WHERE email='$email' LIMIT 1";
        $checkemail_query_run = mysqli_query($con, $checkemail_query);

        if (mysqli_num_rows($checkemail_query_run) > 0) {
            $row = mysqli_fetch_array($checkemail_query_run);
            if ($row['verify_status'] == "0") {
                $name = $row['name'];
                $email = $row['email'];
                $verify_token = $row['verify_token'];

                resend_email_verify($name, $email, $verify_token);

                $_SESSION['status'] = "Verification Email Link has bees sent to your email addess.!!!";
                header("Location: login.php");
                exit(0);
            } else {
                $_SESSION['status'] = "Email already verified. Please login!";
                header("Location: resend_email.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Email not found. Please register first.";
            header("Location: register.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "Please enter the email field";
        header("Location: resend_email.php");
        exit(0);
    }
}
