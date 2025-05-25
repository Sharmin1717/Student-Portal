<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($userEmail, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com';  // SMTP username
        $mail->Password   = 'your_email_password';   // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'YourAppName');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body    = "Please click the link below to verify your email:<br><br>
            <a href='http://yourdomain.com/verify-email.php?code=$verificationCode'>Verify Email</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // You may want to log $mail->ErrorInfo
        return false;
    }
}
?>
