<?php

require_once APPPATH . '../vendor/autoload.php';

class Mail {

    function send($from = "rajeshrkcse@gmail.com", $subject = "Your subject", $body = "Test") {

        //Create the Transport 
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls')
                ->setUsername('rajeshrkcse@gmail.com')
                ->setPassword('rajesh54love@athira');

        //Create the message
        $message = \Swift_Message::newInstance();

        //Give the message a subject
        $message->setSubject($subject)
                ->setFrom($from)
                ->setTo('rajeshrkcse@gmail.com')
                ->setBody($body, 'text/html');
//                ->addPart('<q>Here isC the message sent with swiftmailer</q>', 'text/html');
        //Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);

        //Send the message
        $result = $mailer->send($message);
        if (!$result) {
            return FALSE;
        }

        return TRUE;
    }

}
