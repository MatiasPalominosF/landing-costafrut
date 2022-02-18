<?php
/**
 * Requires the "PHP Email Form" library
 * The "PHP Email Form" library is available only in the pro version of the template
 * The library should be uploaded to: vendor/php-email-form/php-email-form.php
 * For more info and help: https://bootstrapmade.com/php-email-form/
 */

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;

//Load Composer's autoloader
require_once '../assets/vendor/autoload.php';

// data sent in header are in JSON format
header('Content-Type: application/json');

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
    require_once $php_email_form;

} else {
    die('Unable to load the "PHP Email Form" Library!');
}

$receiving_email_address = "palominos90@gmail.com";

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP(); //Send using SMTP
    $mail->Host = 'ssl://smtp.gmail.com'; //Set the SMTP server to send through
    $mail->SMTPAuth = true; //Enable SMTP authentication
    $mail->Username = 'palominos90@gmail.com'; //SMTP username
    $mail->Password = 'zecjmjdqdqyvokff'; //SMTP password
    $mail->SMTPSecure = 'ssl'; //Enable implicit TLS encryption
    $mail->Port = 465;
    //End server settings

    $to = filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL);
    $from_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $from_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $message = nl2br($_POST['message']);

    //Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($to, "SocialTalk");

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();

    if (($mail->send()) === true) {
        echo json_encode(array(
            'success' => true,
            'message' => 'Message sent successfully',
        ));
    } else {
        echo json_encode(array(
            'error' => true,
            'message' => 'Error sending message',
        ));
    }
} catch (Exception $e) {
    if ($e) {
        echo json_encode(array(
            'error' => true,
            'message' => $mail->ErrorInfo,
        ));
    }
}

// $contact = new PHP_Mail_Form;
// $contact->ajax = true;
// $contact->to = $receiving_email_address;
// $contact->from_name = $_POST['name'];
// $contact->from_email = $_POST['email'];
// $contact->subject = $_POST['subject'];

// $contact->add_message($_POST['name'], 'From');
// $contact->add_message($_POST['email'], 'Email');
// $contact->add_message($_POST['message'], 'Message', 10);
// echo $contact->send();

// // Replace contact@example.com with your real receiving email address
// $receiving_email_address = "palominos90@gmail.com";
// $secret_key = '';
// $contact = new PHP_Mail_Form;
// $contact->ajax = true;

// $contact->to = $receiving_email_address;
// $contact->from_name = $_POST['name'];
// $contact->from_email = $_POST['email'];
// $contact->subject = $_POST['subject'];

// $contact->add_message($_POST['name'], 'From');
// $contact->add_message($_POST['email'], 'Email');
// $contact->add_message($_POST['message'], 'Message', 10);

// echo $contact->send();
