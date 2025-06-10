<?php

namespace Smug\FrontendBundle\Subscriber\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Interface\ContentItemRenderingInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemRenderingSubscriber implements EventSubscriberInterface, ContentItemRenderingInterface
{
    public static function getSubscribedEvents(): array
    {
        return [];
    }

    public static function doProcess(array $data, string $identifier): bool
    {
        $moduleIdentifier = $data['module']['module']['identifier'] ?? '';
        return $moduleIdentifier === $identifier;
    }

    public static function doProcessByIncludedContentItems(array $data, string $identifier): bool
    {
        return DataHandler::isInArray($identifier, $data);
    }
}