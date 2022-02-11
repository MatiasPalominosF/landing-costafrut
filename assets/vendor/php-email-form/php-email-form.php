<?php /** * PHP Mail Form * Version: 2.0 * Website: https://templatemag.com/php-mail-form/ * Copyright: TemplateMag.com */

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;

//Load Composer's autoloader
require '../assets/vendor/autoload.php';

class PHP_Mail_Form
{
    public $to = false;
    public $from_name = false;
    public $from_email = false;
    public $subject = false;
    public $mailer = false;
    public $message = '';
    public $content_type = 'text/html';
    public $charset = 'UTF-8';
    public $ajax = false;

    public $error_msg = array(
        'invalid_to_email' =>
        'Email to: is empty or invalid!', 'invalid_from_name' =>
        'From Name is empty!', 'invalid_from_email' =>
        'Email from: is empty or invalid!', 'invalid_subject' =>
        'Subject is too short or empty!', 'invalid_mailer' =>
        'Mailer Email is empty or invalid!', 'short' =>
        'is too short or empty!', 'send_error' =>
        'Could not send mail! Please check your PHP mail configurations.', 'ajax_error' => 'Sorry, the request should be an Ajax POST');
    private $error = false;

    public function __construct()
    {
        $this->mailer = "formsubmit@" . @preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
    }

    public function add_message($content, $label = '', $length_check = false)
    {
        $message = filter_var($content, FILTER_SANITIZE_STRING) . '';
        if ($length_check) {
            if (strlen($message) < $length_check + 4) {
                $this->error .= $label . ' ' . $this->error_msg['short'] . '';
                return;
            }
        }
        $this->message .= !empty($label) ? '' . $label . ': ' . $message : $message;
    }

    public function send()
    {
        if ($this->ajax) {
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                return $this->error_msg['ajax_error'];
            }
        }

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->Username = 'palominos90@gmail.com'; //SMTP username
            $mail->Password = 'zecjmjdqdqyvokff'; //SMTP password
            $mail->SMTPSecure = 'ssl'; //Enable implicit TLS encryption
            $mail->Port = 465;
            //End server settings

            $to = filter_var($this->to, FILTER_VALIDATE_EMAIL);
            $from_name = filter_var($this->from_name, FILTER_SANITIZE_STRING);
            $from_email = filter_var($this->from_email, FILTER_VALIDATE_EMAIL);
            $subject = filter_var($this->subject, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $mailer = filter_var($this->mailer, FILTER_VALIDATE_EMAIL);
            $message = nl2br($this->message);

            if ($to === false && !empty($to)) {
                $this->error .= $this->error_msg['invalid_to_email'] . '';
            }
            if ($from_name === false && !empty($from_name)) {
                $this->error .= $this->error_msg['invalid_from_name'] . '';
            }
            if ($from_email === false && !empty($from_email)) {
                $this->error .= $this->error_msg['invalid_from_email'] . '';
            }
            if ($subject === false && !empty($subject)) {
                $this->error .= $this->error_msg['invalid_subject'] . '';
            }
            if ($mailer === false && !empty($mailer)) {
                $this->error .= $this->error_msg['invalid_mailer'] . '';
            }
            if ($this->error) {
                return $this->error;
            }

            //Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to, "SocialTalk");

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();

            if (($mail->send()) === true) {
                return 'OK';
            } else {
                return $this->error_msg['send_error'];
            }
        } catch (Exception $e) {
            if ($e) {
                return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
}
