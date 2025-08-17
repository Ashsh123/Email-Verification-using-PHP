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


function sendemail_verify($name, $email, $verify_token) {
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
        $mail->setFrom('your-email@gmail.com', 'your-name');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = '
            <h2>Welcome to Our Website, '.$name.'!</h2>
            <p>Thank you for registering. Please click the link below to verify your email address:</p>
            <a href="http://localhost/ashish/Email_Verification/verify_email.php?token='.$verify_token.'">Verify Email</a>
            <p>If you did not register, please ignore this email.</p>
            <p>Best regards,<br>Email Verification Team</p>
        ';

        // Send the email
        $mail->send();
        echo "Verification email sent successfully to $email";
        return true;
}



if (isset($_POST['register_btn'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $verify_token = md5(rand());

    // Email existance check
    $check_email_query  = "SELECT email FROM user_details WHERE email='$email' LIMIT 1";

    $check_email_query_run = mysqli_query($con, $check_email_query);

    if(mysqli_num_rows($check_email_query_run) > 0){
        $_SESSION['status'] = "Email Id already Exists";
        header("Location: register.php");
    }else{
          $query = "INSERT INTO user_details (name, email, phone, password, verify_token) VALUES ('$name', '$email', '$phone', '$password','$verify_token')";
          $query_run = mysqli_query($con, $query);

          if($query_run){
                // Send verification email
                sendemail_verify("$name","$email","$verify_token");
              $_SESSION['status'] = "Registered Successfully!!! Please verify your email Address.";
              header("Location: login.php");

          }else{
             $_SESSION['status'] = "Email Id already Exists";
        header("Location: register.php");
          }
    }


}
?>