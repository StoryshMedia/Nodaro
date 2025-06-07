<?php

namespace Smug\Core\Controller\Backend\Api\Base;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Mail\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use \Exception;
use Smug\AdministrationBundle\Service\Components\Factories\View\View;
use Smug\Core\Context\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BaseController extends AbstractController
{
    const READ_RIGHTS = '';

    const EDIT_RIGHTS = '';

    /** @var Context $context */
    public Context $context;

    /** @var SendMail $sendMail */
    public SendMail $sendMail;

    /** @var EntityManagerInterface $em */
    public EntityManagerInterface $em;
    
    protected EventDispatcherInterface $dispatcher;

    public function __construct(
        protected RouterInterface $router,
        Context $context,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        SendMail $mail
    ) {
        $this->context = $context;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->sendMail = $mail;
    }
	
	/**
	 * @param array $returnData
	 * @return JsonResponse
	 */
    public function prepareReturn(array $returnData): JsonResponse
    {
        $response = new JsonResponse($returnData);
	
	    if (DataHandler::doesKeyExists('code', $returnData)) {
		    $response->setStatusCode($returnData['code']);
        }
	    
        return $response;
    }

    /**
     * @param string $command
     * @param KernelInterface $kernel
     * @param array $parameters
     * @return string
     * @throws Exception
     */
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
}
