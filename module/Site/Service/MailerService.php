<?php

namespace Site\Service;

final class MailerService
{
    /**
     * Sends a message
     * 
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return boolean
     */
    public static function send(string $to, string $subject, string $body) : bool
    {
        // Avoid exception if case invalid email provided
        if (!filter_var($to, \FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $from = [
            $_ENV['from']
        ];

        $transport = \Swift_MailTransport::newInstance(null);

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        // Build Swift's message
        $message = \Swift_Message::newInstance($subject)
                              ->setFrom($from)
                              ->setContentType('text/html')
                              ->setTo($to)
                              ->setBody($body);

        return $mailer->send($message, $failed) != 0;
    }
}
