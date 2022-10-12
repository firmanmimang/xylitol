<?php
require __DIR__.'/PHPMailer/PHPMailer.php';
require __DIR__.'/PHPMailer/SMTP.php';
require __DIR__.'/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function smtp_mail($to, $subject, $message, $from, $debug = false)
{
    $mail = new PHPMailer();
    $mail->SMTPDebug = $debug;
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();

    // hapus semua tujuan, cc, dan bcc
    $mail->clearAddresses();
    $mail->clearCCs();
    $mail->clearBCCs();

    // konfigurasi dasar SMTP
    $mail->SMTPAuth = "true";
    $mail->SMTPSecure = "tls";

    $mail->Host = "lottexylitolsmile.id;103.30.247.50";
    $mail->Port = "587";

    $mail->Username = "no-reply@lottexylitolsmile.id";
    $mail->Password = "Y*I^vdixKO!q";

    $default_email_from = "no-reply@lottexylitolsmile.id";

    if(empty($from)) $mail->setFrom($default_email_from);
    else $mail->setFrom($from);
    
    $mail->Subject = $subject;
    
    $mail->Body = $message;
    $mail->IsHTML(true);

    $mail->addAddress($to);
    
    if ($mail->Send()) {
        echo "Email Sent..!";
    } else {
        echo "Error..!";
    }

    $mail->smtpClose();
}