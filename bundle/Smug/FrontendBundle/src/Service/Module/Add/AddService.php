<?php

namespace Smug\FrontendBundle\Service\Module\Add;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\FrontendBundle\Entity\ModuleField\ModuleField;
use Smug\FrontendBundle\Entity\ModuleTab\ModuleTab;

class AddService
{
    public function installModuleFields(array $fields, Context $context, Module|ContentItemModule $module, bool $plugin = false, $entityIdentifier = 'field'): void
    {
        foreach ($fields ?? [] as $field) {
            $fieldObject = $context->getEntityByMultiple([
                'identifier' => $field['identifier'],
                'module' => $module
            ], $entityIdentifier);

            if (DataHandler::isEmpty($fieldObject)) {
                $fieldObject = (DataHandler::isInstanceOf($module, ContentItemModule::class)) ? new ContentItemModuleField: new ModuleField();
            }

            $fieldObject->__set('identifier', $field['identifier']);
            $fieldObject->__set('placeholder', $field['placeholder'] ?? '');
            $fieldObject->__set('description', $field['description'] ?? '');
            $fieldObject->__set('type', $field['type']);
            
            if (!DataHandler::isInstanceOf($module, ContentItemModule::class)) {
                $fieldObject->__set('defaultValue', $field['default'] ?? '');
            }
            
            $fieldObject->__set('module', $module);
            $fieldObject->__set('isPlugin', $plugin);
            $fieldObject->__set('config', DataHandler::getJsonEncode($field['config'] ?? []));
            $fieldObject->__set('settings', DataHandler::getJsonEncode($field['settings'] ?? []));
            $fieldObject->__set('classes', DataHandler::getJsonEncode($field['classes'] ?? []));

            $context->getEntityManager()->persist($fieldObject);
            $context->getEntityManager()->flush();

            if ($field['type'] === 'Tabs') {
                foreach ($field['config']['fields'] ?? [] as $child) {
                    $childObject = $context->getEntityByMultiple([
                        'identifier' => $child['identifier'],
                        'module' => $module,
                        'parent' => $fieldObject
                    ], 'field');

                    if (DataHandler::isEmpty($childObject)) {
                        $childObject = new ModuleField();
                    }

                    $childObject->__set('identifier', $child['identifier']);
                    $childObject->__set('placeholder', $child['placeholder'] ?? '');
                    $childObject->__set('type', $child['type']);
                    $childObject->__set('module', $module);
                    $childObject->__set('parent', $fieldObject);
                    $childObject->__set('config', DataHandler::getJsonEncode($child['config'] ?? []));

                    $context->getEntityManager()->persist($childObject);
                    $context->getEntityManager()->flush();
                }
            }
        }
    }

    public function installTabs(array $tabs, Context $context, Module|ContentItemModule $module, bool $plugin = false, $entityIdentifier = 'field') {
        foreach ($tabs as $tab) {
            $newTab = new ModuleTab();

            $newTab->__set('module', $module);

            $context->getEntityManager()->persist($newTab);
            $context->getEntityManager()->flush();

            foreach ($tab['fields'] as $field) {
                $fieldObject = $context->getEntityByMultiple([
                    'identifier' => $field['identifier'],
                    'tab' => $newTab
                ], $entityIdentifier);
            
                if (DataHandler::isEmpty($fieldObject)) {
                    $fieldObject = (DataHandler::isInstanceOf($module, ContentItemModule::class)) ? new ContentItemModuleField: new ModuleField();
                }
                
                $fieldObject->__set('identifier', $field['identifier']);
                $fieldObject->__set('placeholder', $field['placeholder'] ?? '');
                $fieldObject->__set('description', $field['description'] ?? '');
                $fieldObject->__set('type', $field['type']);
                $fieldObject->__set('tab', $newTab);
                $fieldObject->__set('isPlugin', $plugin);
                $fieldObject->__set('defaultValue', $field['default'] ?? '');
                $fieldObject->__set('config', DataHandler::getJsonEncode($field['config'] ?? []));
                $fieldObject->__set('settings', DataHandler::getJsonEncode($field['settings'] ?? []));
                $fieldObject->__set('classes', DataHandler::getJsonEncode($field['classes'] ?? []));

                $context->getEntityManager()->persist($fieldObject);
                $context->getEntityManager()->flush();
            }
        }
    }
}
