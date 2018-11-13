<?php
require_once "Mail.php";
require_once 'Mail/mime.php';

include_once(ROOT_PATH . "/public_html/wordpress/wp-load.php");

class MailSender
{
    private $_smtp;
    private $_letterPatterns;
    private $_smtpAcc;
    private $_headers;
    private $_body;
    private $_to;
    private $_notify;
    private $_mimeParams = array('text_encoding' => '7bit',
                                  'text_charset'  => 'UTF-8',
                                  'html_charset'  => 'UTF-8',
                                  'head_charset'  => 'UTF-8');

    public function __construct()
    {
        $config = json_decode(file_get_contents(APPLICATION_PATH . "/configs/config.json"), true);
        $this->_letterPatterns = $config['sendLetter'];
        $this->_notify = $config['notify'];
        $smtpCreden = $config['SMTP'];
        $this->_smtpAcc = $smtpCreden["account"];
        $this->_smtp = Mail::factory('smtp', array(
                                    'host' => $smtpCreden['host'],
                                    'port' => $smtpCreden["port"],
                                    'auth' => true,
                                    'username' => $this->_smtpAcc,
                                    'password' => $smtpCreden["password"])
                                );
    }

    private function _formHeaders($sendTo, $subject)
    {
        $from = '<' . $this->_smtpAcc . '>';
        $to = '<' . $sendTo . '>';
        $headers = 'From: My Name ' . $from . "\r\n";
    }

    private function _formBody($letterKey, $filePath)
    {
        $crlf = "\n";
        $text = file_get_contents(ROOT_PATH .
                 $this->_letterPatterns[$letterKey]['letterTextPath']);
        $mime = new Mail_mime(array('eol' => $crlf));
        $mime->setHTMLBody($text);
        if(file_exists($filePath)){
            $mime->addAttachment($filePath, 'application/pdf ');
        }
        $this->_body = $mime->get($this->_mimeParams);
        $this->_headers = $mime->headers($this->_headers);
    }

    public function sendTo($email, $letterKey, $filePath)
    {
        $subject = $this->_letterPatterns[$letterKey]['subject'];
        $message = file_get_contents(ROOT_PATH .
                 $this->_letterPatterns[$letterKey]['letterTextPath']);
        $from = '<' . $this->_smtpAcc . '>';
        $headers = 'From: My Name ' . $from . "\r\n";
        $attachments = array($filePath);
        $mail = wp_mail($this->_to,$subject,
            $message, $headers, $attachments);
        if (PEAR::isError($mail))
        {
            return '<p>' . $mail->getMessage() . '</p>';
        }
        return true;
    }

    private function _notifyBody($options)
    {
        $text = sprintf($this->_notify['text'], $options['name'], $options['phone'],$options['email']);
        $crlf = "\r\n";
        $mime = new Mail_mime(array('eol' => $crlf));
        $mime->setTXTBody($text);
        $this->_body = $mime->get($this->_mimeParams);
        $this->_headers = $mime->headers($this->_headers);
    }

    public function notifyAboutRequest($options)
    {
        $this->_formHeaders($this->_smtpAcc, $this->_notify['subject']);
        $this->_notifyBody($options);
        $mail = $this->_smtp->send($this->_smtpAcc, $this->_headers, $this->_body);
        if (PEAR::isError($mail))
        {
            return '<p>' . $mail->getMessage() . '</p>';
        }
        return true;
    }
}
