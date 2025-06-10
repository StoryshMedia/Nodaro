<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\MediaContentItemModuleFieldAssociation\MediaContentItemModuleFieldAssociation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(ContentItem::class)) {
            $requestData = $event->getContext()->getRequestData();

            $event->getContext()->addRepository('contentItem', EntityGenerator::getGeneratedEntity(ContentItem::class));
            $event->getContext()->addRepository('media', EntityGenerator::getGeneratedEntity(Media::class));
            $event->getContext()->addRepository('mediaAssociation', EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class));
            $event->getContext()->addRepository('contentItemField', EntityGenerator::getGeneratedEntity(ContentItemModuleField::class));
            
            $this->updateContentElements($event, $requestData);
        }
    }

    protected function updateContentElements(DataUpdatedEvent $event, array $contentItemData)
    {
        if (!DataHandler::doesKeyExists('id', $contentItemData)) {
            foreach ($contentItemData as $column) {
                foreach ($column as $itemData) {
                    $this->updateContentElement($event, $itemData);
                }
            }
        } else {
            $this->updateContentElement($event, $contentItemData);
        }
    }

    protected function updateContentElement(DataUpdatedEvent $event, array $contentItemData)
    {
        $contentItem = $event->getContext()->getEntityByIdentifier($contentItemData['id'], 'id', 'contentItem');

        $contentItem->__set('title', $contentItemData['title']);
        $contentItem->__set('position', $contentItemData['position']);
        $contentItem->__set(
            'templateClasses',
            DataHandler::isString($contentItemData['templateClasses']) ? $contentItemData['templateClasses'] : DataHandler::getJsonEncode($contentItemData['templateClasses'])
        );
        $contentItem->__set(
            'additionalClasses',
            DataHandler::isString($contentItemData['additionalClasses']) ? $contentItemData['additionalClasses'] : DataHandler::getJsonEncode($contentItemData['additionalClasses'])
        );

        $event->getContext()->getEntityManager()->persist($contentItem);
        $event->getContext()->getEntityManager()->flush();

        if (!DataHandler::isEmpty($contentItemData['children'] ?? [])) {
            $this->updateContentElements($event, $contentItemData['children']);
        }
    }
}