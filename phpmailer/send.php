<?php

require_once 'Exception.php';
require_once 'PHPMailer.php';
require_once 'SMTP.php';

function sendEmail($to, $title, $body)
{

    $statusMassage = '';

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {

        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'evartanyan1989@gmail.com';
        $mail->Password = 'yjekqwshdtzgjzgw';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('evartanyan1989@gmail.com', 'Cloud Storage');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $body;

        $mail->send();


        return 'Сообщение отправлено';


    } catch (Exception $e) {

        return "Сообщение не было отправлено. Причина: {$mail->ErrorInfo}";

    }



}