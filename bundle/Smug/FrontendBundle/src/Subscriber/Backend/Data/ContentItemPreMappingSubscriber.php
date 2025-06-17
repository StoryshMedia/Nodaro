<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataPreMappingEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\ContentItemModuleTab\ContentItemModuleTab;
use Smug\FrontendBundle\Entity\Module\Module;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemPreMappingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_PRE_MAPPING => 'onDataPreMapped'
        ];
    }

    public function onDataPreMapped(DataPreMappingEvent $event): void
    {
		$class = EntityGenerator::getGeneratedEntity(ContentItem::class);

        if ($event->getClass() === $class) {
            $requestData = $event->getContext()->getRequestData();

            $moduleClass = EntityGenerator::getGeneratedEntity(Module::class);
            $event->getContext()->addRepository('mainModule', $moduleClass);
            $mainModuleData = $requestData['module'];

            $mainModule = (DataHandler::isArray($mainModuleData)) ? $event->getContext()->getEntityByIdentifier($mainModuleData['identifier'], 'identifier', 'mainModule') : $mainModuleData;
            
            $contentItemModuleClass = EntityGenerator::getGeneratedEntity(ContentItemModule::class);
            $module = new $contentItemModuleClass();
            $module->__set('module', $mainModule);

            $event->getContext()->getEntityManager()->persist($module);
            $event->getContext()->getEntityManager()->flush();

            $fieldClass = EntityGenerator::getGeneratedEntity(ContentItemModuleField::class);
            $tabClass = EntityGenerator::getGeneratedEntity(ContentItemModuleTab::class);

            foreach ($mainModuleData['fields'] as $fieldData) {
                $field = new $fieldClass();

                $field->__set('type', $fieldData['type']);
                $field->__set('module', $module);
                $field->__set('config', DataHandler::getJsonEncode($fieldData['config']));
                $field->__set('settings', DataHandler::getJsonEncode($fieldData['settings']));
                $field->__set('classes', DataHandler::getJsonEncode($fieldData['classes']));
                $field->__set('placeholder', $fieldData['placeholder']);
                $field->__set('description', $fieldData['description'] ?? '');
                $field->__set('parentId', null);
                $field->__set('value', $fieldData['defaultValue']);
                $field->__set('isPlugin', $fieldData['isPlugin']);
                $field->__set('identifier', $fieldData['identifier']);
                $event->getContext()->getEntityManager()->persist($field);
                $event->getContext()->getEntityManager()->flush();

                $module->__add('fields', $field);
            }

            foreach ($mainModuleData['tabs'] ?? [] as $tab) {
                $newTab = new $tabClass();
    
                $newTab->__set('module', $module);
    
                $event->getContext()->getEntityManager()->persist($newTab);
                $event->getContext()->getEntityManager()->flush();

                foreach ($tab['fields'] as $tabField) {
                    $newField = new $fieldClass();

                    $newField->__set('type', $tabField['type']);
                    $newField->__set('tab', $newTab);
                    $newField->__set('config', DataHandler::getJsonEncode($tabField['config']));
                    $newField->__set('settings', DataHandler::getJsonEncode($tabField['settings']));
                    $newField->__set('classes', DataHandler::getJsonEncode($tabField['classes']));
                    $newField->__set('placeholder', $tabField['placeholder']);
                    $newField->__set('description', $tabField['description'] ?? '');
                    $newField->__set('parentId', null);
                    $newField->__set('value', $tabField['defaultValue']);
                    $newField->__set('isPlugin', $tabField['isPlugin']);
                    $newField->__set('identifier', $tabField['identifier']);
                    $event->getContext()->getEntityManager()->persist($newField);
                    $event->getContext()->getEntityManager()->flush();

                    $newTab->__add('fields', $newField);
                }
            }

            $requestData['module'] = $module->toArray();

            if ($requestData['rowColumn'] === null) {
                $requestData['rowColumn'] = 0;
            }

            $event->getContext()->setRequestData($requestData);
        }
    }
}