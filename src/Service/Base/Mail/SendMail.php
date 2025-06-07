<?php

namespace Smug\Core\Service\Base\Mail;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\Email\SendinblueService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SendMail
 * @package Smug\Core\Service\Base\Mail
 */
class SendMail extends AbstractController
{
    /** @var ContainerInterface $container */
    protected $container;
    
    /** @var LoggerInterface $logger */
    protected LoggerInterface $logger;

    /**
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->logger = $logger;
    	$this->container = $kernel->getContainer();
    }
	
	/**
	 * @param $template
	 * @param array $mailData
	 * @param array $data
	 * @param array|null $attachment
	 * @return bool
	 */
    public function sendHtmlMail($template, array $mailData, array $data, array $attachment = null): bool
    {
        $recipients = DataHandler::doesKeyExists('recipient', $mailData) ? [$mailData['recipient']] : $mailData['recipients'];
        $attachmentList = [];

        $sendInBlueData = [
            'sender' => [
                'name' => (!empty($mailData['senderName'])) ? $mailData['senderName'] : 'Storysh',
                'email' => $mailData['from']
            ],
            'recipients' => $recipients,
            'subject' => $mailData['subject'],
            'preview' => (!empty($mailData['preview'])) ? $mailData['preview'] : 'Informationen von Deinem Storysh Team',
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
