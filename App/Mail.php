<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use App\Config;

/**
 * Mail
 *
 * PHP version 7.2.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return boolean true if email sent, false otherwise
     */
    public static function send($to, $subject, $text, $html)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
                );

            $mail->SMTPDebug = SMTP::DEBUG_OFF;                                       
            $mail->isSMTP();                                            
            $mail->Host       = Config::PHPMAILER_HOST;                    
            $mail->SMTPAuth   = true;                             
            $mail->Username   = Config::PHPMAILER_USERNAME;                 
            $mail->Password   = Config::PHPMAILER_PASSWORD;                        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                              
            $mail->Port       = 587;  
         
            //Recipient
            $mail->setFrom(Config::PHPMAILER_USERNAME, Config::PHPMAILER_SENDER_NAME);           
            $mail->addAddress($to);
         //   $mail->addAddress('receiver2@gfg.com', 'Name');
            
            //Content
            $mail->isHTML(true);                                  
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;
           
            $mail->send();
            return true;

        } catch (Exception $e) {
            return false;
        }
 
     /*
        $mg = new Mailgun(Config::MAILGUN_API_KEY);
        $domain = Config::MAILGUN_DOMAIN;

        $mg->sendMessage($domain, ['from'    => 'your-sender@your-domain.com',
                                   'to'      => $to,
                                   'subject' => $subject,
                                   'text'    => $text,
                                   'html'    => $html]);
    */
    }
}
