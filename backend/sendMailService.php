<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "./config/config.php";

class EmailSender {
    private $config;
    private $data;
    private $mail;

    public function __construct($data){

        $this->config = getConfig('MAIL_SERVICE');
        $this->data   = $data;
        $this->mail   = new PHPMailer(true);

        $this->mail->isSMTP();
	//$this->mail->SMTPDebug = 2;
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
	$this->mail->AuthType = 'LOGIN';
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
    }

    public function sendEmail()
    {
        try {
            // Check if the required valus are set in the configuration File.
            if(
                (!isset($this->config['_ORG_MAIL_']) && $this->config['_ORG_MAIL_'] === "" ) ||                
                (!isset($this->config['_ORG_M_PASS_']) && $this->config['_ORG_M_PASS_'] === "" ) ||                
                (!isset($this->config['_RECP_EMAIL_']) && $this->config['_RECP_EMAIL_'] === "" ) 
            ) return "Confugurations Are Not Provided";

            $this->mail->Username = $this->config['_ORG_EMAIL_'];
            $this->mail->Password = $this->config['_ORG_M_PASS_'];
            $this->mail->setFrom($this->data['email'], $this->data['name']);
            $this->mail->addAddress($this->config['_RECP_EMAIL_']);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'New Contact Form Submission';
            $this->mail->Body = "Name: {$this->data['name']}<br>Email: {$this->data['email']}<br>Message: {$this->data['message']}<br>{$this->data['phone']}";

            // Send the email
            $this->mail->send();
            return "true";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>
