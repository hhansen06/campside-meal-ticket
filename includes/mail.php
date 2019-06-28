<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 20.09.2018
 * Time: 12:30
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function send_mail($empfaenger,$betreff,$texthtml)
{
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.1und1.de';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@bildung.diepholz.de';                 // SMTP username
        $mail->Password = '3YQ4oULbB4pwzXCBX7R';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('no-replay@bildung.diepholz.de', 'Bildungsportal');
        $mail->addAddress($empfaenger);     // Add a recipient

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $betreff;
        $mail->Body    = $texthtml;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}