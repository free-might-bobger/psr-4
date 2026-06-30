<?php
namespace App\Mail;
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailerWrapper {

    public static function register($userInfo) {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer( true );

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            //Enable verbose debug output
            $mail->isSMTP();
            //Send using SMTP
            $mail->Host       = 'smtp.hostinger.com';
            //Set the SMTP server to send through
            $mail->SMTPAuth   = true;
            //Enable SMTP authentication
            $mail->Username   = 'admin@shaischool.net';
            //SMTP username
            $mail->Password   = 'Shai7120/';
            //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            //Enable implicit TLS encryption
            $mail->Port       = 465;
            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $name = $userInfo['name'];
            $email = $userInfo['email'];
            $activationCode = $userInfo['activation_code'];

            //Recipients
            $mail->setFrom( 'admin@shaischool.net' );
            //Add a recipient
            $mail->addAddress( $email, $name);
            
            //Content
            $mail->isHTML( true );
            //Set email format to HTML
            $mail->Subject = 'Shai School Account';
            $mail->Body    = "
                Hi, Welcome $name <br />
                To verify your email address $email, please click the following link. <br />
                <a href='$activationCode'>Verify Email</a>
                <br />
                <br />
                Or copy and paste in the browser: $activationCode
            ";
            $mail->send();
        } catch ( Exception $e ) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
