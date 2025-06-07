<?php

namespace Smug\Core\Service\Base\Service\Email;

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class MailerSendService
{
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
