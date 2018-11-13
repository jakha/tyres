<?php
require_once APPLICATION_PATH . "/models/Filters.php";

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
    $email = $_POST['email'];
    $fileName = $_POST['file'];
    recordUserAction($email, '', '', $fileName);

    switch ('var') {
        case 'value':
            // code...
            break;
        default:
            $letterKey = 'defaultText';
            break;
    }
    //attachment
    if($fileName == 'catalog')
        $fileName = "Catalogue";
    else
        $fileName = "MICHELIN_" . str_ireplace(" ", "_", $fileName);

    $filePath = ROOT_PATH . "/public_html/data/" . $fileName . ".pdf";
    $result = sendTo($email,$letterKey,$filePath);
    header('Content-Type: application/json');
    echo json_encode($result);
    return;
}

function sendTo($email,$letterKey,$filePath)
{
    $config = json_decode(file_get_contents(APPLICATION_PATH . "/configs/config.json"), true);
    $letterPatterns = $config['sendLetter'];
    $subject = $letterPatterns[$letterKey]['subject'];
    $smtpCreden = $config['SMTP'];
    $smtpAcc = $smtpCreden["account"];
    $message = file_get_contents(ROOT_PATH .
             $letterPatterns[$letterKey]['letterTextPath']);
    $from = '<' . $smtpAcc . '>';
    $to = $email;
    $headers = 'From: My Name ' . $from . "\r\n";
    $attachments = array($filePath);
    $mail = wp_mail($to,$subject,
        $message, $headers, $attachments);
    return $mail;
}
