<?php

require_once APPLICATION_PATH . "/models/Filters.php";

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
    $options['email'] = $_POST['email'];
    $options['phone'] = $_POST['phone'];
    $options['name'] = $_POST['name'];
    recordUserAction($options['email'],$options['phone'],$options['name']);
    $result = notifyAboutRequest($options);
    header('Content-Type: application/json');
    echo json_encode($result);
    return;
}

function notifyAboutRequest($options)
{
    $config = json_decode(file_get_contents(APPLICATION_PATH . "/configs/config.json"), true);
    $notify = $config['notify'];
    $letterPatterns = $config['sendLetter'];
    $subject = $notify['subject'];
    $smtpCreden = $config['SMTP'];
    $smtpAcc = $smtpCreden["account"];
    $message = sprintf($notify['text'], $options['name'],
                    $options['phone'],$options['email']);
    $from = '<' . $smtpAcc . '>';
    $headers = 'From: My Name ' . $from . "\r\n";
    $mail = wp_mail($smtpAcc,$subject, $message, $headers);
    return $mail;
}
