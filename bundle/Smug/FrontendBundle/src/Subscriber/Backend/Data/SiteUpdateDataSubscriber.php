<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\Core\Service\Base\Components\Factory\Entity\AssociationMapperFactory;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\MediaContentItemModuleFieldAssociation\MediaContentItemModuleFieldAssociation;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Site::class)) {
            $requestData = $event->getContext()->getRequestData();

            $event->getContext()->addRepository('contentItem', EntityGenerator::getGeneratedEntity(ContentItem::class));
            $event->getContext()->addRepository('media', EntityGenerator::getGeneratedEntity(Media::class));
            $event->getContext()->addRepository('mediaAssociation', EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class));
            $event->getContext()->addRepository('contentItemField', EntityGenerator::getGeneratedEntity(ContentItemModuleField::class));
            
            $this->updateContentElements($event, $requestData['contentItems']);
        }
    }

    protected function updateContentElements(DataUpdatedEvent $event, array $contentItems)
    {
        foreach ($contentItems as $contentItemData) {
            if (DataHandler::isEmpty($contentItemData)) {
                continue;
            }
            
            if (!DataHandler::doesKeyExists('id', $contentItemData)) {
                foreach ($contentItemData as $itemData) {
                    $this->updateContentElement($event, $itemData);
                }

                continue;
            }
            $this->updateContentElement($event, $contentItemData);
        }
    }

    protected function updateContentElement(DataUpdatedEvent $event, array $contentItemData)
    {
        $contentItem = $event->getContext()->getEntityByIdentifier($contentItemData['id'], 'id', 'contentItem');

        $contentItem->__set('title', $contentItemData['title']);
        $contentItem->__set('position', $contentItemData['position']);
        $contentItem->__set('templateClasses', DataHandler::getJsonEncode($contentItemData['templateClasses']));
        $contentItem->__set('additionalClasses', DataHandler::getJsonEncode($contentItemData['additionalClasses'] ?? '[]'));

        $event->getContext()->getEntityManager()->persist($contentItem);
        $event->getContext()->getEntityManager()->flush();

        foreach ($contentItemData['module']['fields'] as $fieldData) {
            $field = $event->getContext()->getEntityByIdentifier($fieldData['id'], 'id', 'contentItemField');
            $field->__set('value', DataHandler::getStringValue($fieldData['value']));
            $field->__set('settings', $fieldData['settings']);
            $field->__set('classes', $fieldData['classes']);

            if (!DataHandler::isEmpty($fieldData['files'])) {
                AssociationMapperFactory::handleMappingStates(
                    $field,
                    $fieldData['files'],
                    $event->getContext(),
                    [
                        'property' => 'files',
                        'multiple' => $fieldData['config']['multiple'] ?? false,
                        'associationBaseIdentifier' => 'media',
                        'associationIdentifier' => 'mediaAssociation',
                        'associationClass' => EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class)
                    ]
                );
            }
            $event->getContext()->getEntityManager()->persist($field);
            $event->getContext()->getEntityManager()->flush();
        }

        if (!DataHandler::isEmpty($contentItemData['children'] ?? [])) {
            $this->updateContentElements($event, $contentItemData['children']);
        }

        foreach ($contentItemData['module']['tabs'] as $tab) {
            foreach ($tab['fields'] as $fieldData) {
                $field = $event->getContext()->getEntityByIdentifier($fieldData['id'], 'id', 'contentItemField');
                $field->__set('value', DataHandler::getStringValue($fieldData['value']));
                $field->__set('settings', $fieldData['settings']);
                $field->__set('classes', $fieldData['classes']);

                if (!DataHandler::isEmpty($fieldData['files'])) {
                    AssociationMapperFactory::handleMappingStates(
                        $field,
                        $fieldData['files'],
                        $event->getContext(),
                        [
                            'property' => 'files',
                            'multiple' => $fieldData['config']['multiple'] ?? false,
                            'associationBaseIdentifier' => 'media',
                            'associationIdentifier' => 'mediaAssociation',
                            'associationClass' => EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class)
                        ]
                    );
                }
                $event->getContext()->getEntityManager()->persist($field);
                $event->getContext()->getEntityManager()->flush();
            }
        }
    }
}