<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Events\Backend\Data\DataPreUpdatedEvent;
use Smug\Core\Service\Base\Components\Factory\Entity\AssociationMapperFactory;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\MediaContentItemModuleFieldAssociation\MediaContentItemModuleFieldAssociation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemPreUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_PRE_UPDATE => 'onDataPreUpdated'
        ];
    }

    public function onDataPreUpdated(DataPreUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(ContentItem::class)) {
            $requestData = $event->getContext()->getRequestData();

            $event->getContext()->addRepository('contentItem', EntityGenerator::getGeneratedEntity(ContentItem::class));
            $event->getContext()->addRepository('media', EntityGenerator::getGeneratedEntity(Media::class));
            $event->getContext()->addRepository('mediaAssociation', EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class));
            $event->getContext()->addRepository('contentItemField', EntityGenerator::getGeneratedEntity(ContentItemModuleField::class));
            
            $this->updateContentElements($event, $requestData);
        }

        if ($event->getClass() === EntityGenerator::getGeneratedEntity(ContentItemModuleField::class)) {
            $requestData = $event->getContext()->getRequestData();
            $event->getContext()->addRepository('media', EntityGenerator::getGeneratedEntity(Media::class));
            $event->getContext()->addRepository('mediaAssociation', EntityGenerator::getGeneratedEntity(MediaContentItemModuleFieldAssociation::class));
            $event->getContext()->addRepository('contentItemField', EntityGenerator::getGeneratedEntity(ContentItemModuleField::class));

            $this->updateContentElementField($event, $requestData);
        }
    }

    protected function updateContentElements(DataPreUpdatedEvent $event, array $contentItemData)
    {
        if (!DataHandler::doesKeyExists('id', $contentItemData)) {
            foreach ($contentItemData as $itemData) {
                $this->updateContentElementFields($event, $itemData);
            }
        } else {
            $this->updateContentElementFields($event, $contentItemData);
        }
    }

    protected function updateContentElementFields(DataPreUpdatedEvent $event, array $contentItemData)
    {
        foreach ($contentItemData['module']['fields'] as $fieldData) {
            $this->updateContentElementField($event, $fieldData);
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

    protected function updateContentElementField(DataPreUpdatedEvent $event, array $fieldData)
    {
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