<?php

namespace Smug\Core\Controller\Backend\Api\Base;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Service\Components\Factories\View\View;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\AdministrationBundle\Trait\RequestParameterTrait;
use Smug\Core\Context\Context;
use Smug\Core\Events\Email\EmailSendEvent;
use Smug\Core\Service\Email\EmailData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BaseController extends AbstractController
{
    use DispatchDataTrait;
    use RequestParameterTrait;

    const READ_RIGHTS = '';

    const EDIT_RIGHTS = '';

    public Context $context;

    public EntityManagerInterface $em;

    public MailerInterface $mailer;
    
    protected EventDispatcherInterface $dispatcher;

    public function __construct(
        protected RouterInterface $router,
        Context $context,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        MailerInterface $mailer
    ) {
        $this->context = $context;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
    }
	
    public function prepareReturn(array $returnData): JsonResponse
    {
        $response = new JsonResponse($returnData);
	
	    if (DataHandler::doesKeyExists('code', $returnData)) {
		    $response->setStatusCode($returnData['code']);
        }
	    
        return $response;
    }

    public function runCliCommand(string $command, KernelInterface $kernel, array $parameters = []): string
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arrayInputData = [
            'command' => $command
        ];

        if (!DataHandler::isEmpty($parameters)) {
            $arrayInputData = DataHandler::mergeArray($arrayInputData, $parameters);
        }


        $input = new ArrayInput($arrayInputData);

        $output = new BufferedOutput();
        $application->run($input, $output);

        return $output->fetch();
    }

    public function dispatch(Event $event, ?string $eventName = null) {
        $this->dispatcher->dispatch($event, $eventName);
    }

    public static function bypassIdToConfigFields(string $id, View $config): View
    {
        foreach ($config->getTabs() as $tab) {
            foreach ($tab->getRows() as $row) {
                foreach ($row->getFields() as $field) {
                    if ($field->getConfigItem('bypassId') === true) {
                        $field->addConfigItem('id', $id);
                    }
                }
            }
        }

        return $config;
    }
    
    public function sendHtmlMail(EmailData $emailData, ?array $attachment = null): bool
    {
        $attachmentList = [];

        $data = [
            'sender' => $emailData->__get('sender'),
            'recipients' => $emailData->__get('recipients'),
            'subject' => $emailData->__get('subject'),
            'preview' => 'Informationen von ' . $emailData->__get('sender')['name'],
            'body' => $this->render(
                $emailData->__get('template'),
                ['data' => $emailData->__get('data')]
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
            $emaildata['attachment'] = $attachmentList;
        }

        return $this->send($data);
    }

    protected function send(array $emailData): bool
    {
        $emailData['isSend'] = false;

        $emailData = $this->dispatchData(
            $emailData,
            $this->context,
            EmailSendEvent::class,
            '',
            SystemEvents::SEND_EMAIL_DATA
        );

        // if the email was not send by other bundles use the base symfoy fallback email sender
        if (!$emailData['isSend']) {
            $recipients = self::getEmailRecipients($emailData['recipients']);

            $email = new Email();
            $email->from(new Address($emailData['sender']['email'], $emailData['sender']['name']))
                ->to(...$recipients)
                ->subject($emailData['subject'])
                ->html($emailData['body']);

            foreach ($emaildata['attachment'] ?? [] as $attachment) {
                $email->attach($attachment['content'], $attachment['name'] ?? '');
            }

            $this->mailer->send($email);
        }

        return true;
    }

    private static function getEmailRecipients(array $recipients): array
    {
        $addresses = [];

        foreach ($recipients as $recipient) {
            $addresses[] = new Address($recipient['email'], $recipient['name'] ?? '');
        }

        return $addresses;
    }
}
