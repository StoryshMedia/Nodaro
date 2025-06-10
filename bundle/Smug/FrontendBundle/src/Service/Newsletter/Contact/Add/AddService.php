<?php

namespace Smug\FrontendBundle\Service\Newsletter\Contact\Add;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Service\AddBaseService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\Email\SendinblueService;

/**
 * Class AddService
 * @package Smug\FrontendBundle\Service\Newsletter\Contact\Add
 */
class AddService extends AddBaseService
{
    /**
     * @inheritDoc
     */
    public function add(Context $context, $import = false): array
    {
        $addContact = SendinblueService::addContact($context->getRequestData());

        if (!DataHandler::isEmpty($addContact)) {
            return ['success' => false];
        }

        return ['success' => true];
    }
}
