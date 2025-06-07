<?php

namespace Smug\Core\Service\Base\Service\Email;

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class MailerSendService
{
    const API_KEY = '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';

    const SEND_IN_BLUE_SEND_EMAIL_API_URL = 'https://api.sendinblue.com/v3/smtp/email';

    /**
     * @param array $data
     * @return bool|string
     */
    public static function send(array $data)
    {
        $mailersend = new MailerSend(['api_key' => self::API_KEY]);

        foreach ($data['recipients'] as $recipient) {
            $recipients = [
                new Recipient($recipient['email'], ''),
            ];
        }

        $emailParams = (new EmailParams())
            ->setFrom($data['sender']['email'])
            ->setFromName($data['sender']['name'])
            ->setRecipients($recipients)
            ->setSubject($data['subject'])
            ->setHtml($data['body'])
            ->setReplyTo($data['sender']['email'])
            ->setReplyToName($data['sender']['name']);

        return $mailersend->email->send($emailParams);
    }
}
