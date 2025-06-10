<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataDeletedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Service\Factory\ContentItemPositionFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemDeleteDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_DELETED => 'onDataDeleted'
        ];
    }

    public function onDataDeleted(DataDeletedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(ContentItem::class)) {
            if (!DataHandler::isEmpty($event->getData()['site'])) {
                ContentItemPositionFactory::renewPositions($event->getContext(), $event->getData(), 'site');
            }
        }
    }
}