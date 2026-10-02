<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
       
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';         
        $mail->SMTPAuth   = true;
        $mail->Username   = 'asadbashir200003@gmail.com';   
        $mail->Password   = 'lxcq bgvc nfop ecid';      
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

       
        $mail->setFrom('your_email@gmail.com', 'HEC Laptop Portal');
        $mail->addAddress($to);

        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
