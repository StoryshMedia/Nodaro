<?php

namespace Smug\Core\Service\Base\Mail;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\Email\SendinblueService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SendMail extends AbstractController
{
    /** @var ContainerInterface $container */
    protected $container;
    
    /** @var LoggerInterface $logger */
    protected LoggerInterface $logger;

    public function __construct(KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->logger = $logger;
    	$this->container = $kernel->getContainer();
    }
	
    public function sendHtmlMail($template, array $mailData, array $data, ?array $attachment = null): bool
    {
        $recipients = DataHandler::doesKeyExists('recipient', $mailData) ? [$mailData['recipient']] : $mailData['recipients'];
        $attachmentList = [];

        $senderName = (!DataHandler::isEmpty($mailData['senderName'])) ? $mailData['senderName'] : 'Sender';

        $sendInBlueData = [
            'sender' => [
                'name' => $senderName,
                'email' => $mailData['from']
            ],
            'recipients' => $recipients,
            'subject' => $mailData['subject'],
            'preview' => (!DataHandler::isEmpty($mailData['preview'])) ? $mailData['preview'] : 'Informationen von ' . $senderName,
            'body' => $this->render(
                $template,
                $data
            )->getContent()
        ];

        if ($attachment !== null) {
            foreach ($attachment as $item) {
                if (DataHandler::checkFile($item['path'])) {
                    $attachmentList[] = [
                        'name' => $item['fileName'],
                        'content' => chunk_split(base64_encode(DataHandler::getFile($item['path'])))
                    ];
                }
            }
        }

        if (!empty($attachmentList)) {
            $sendInBlueData['attachment'] = $attachmentList;
        }

        $this->logger->alert(SendinblueService::send($sendInBlueData));

        return true;
    }
}
