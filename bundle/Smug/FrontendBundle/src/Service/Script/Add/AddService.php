<?php

namespace Smug\FrontendBundle\Service\Script\Add;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Script\Script;
use Smug\FrontendBundle\Entity\ScriptField\ScriptField;

class AddService
{
    public function installScriptFields(array $fields, Context $context, Script $script, $entityIdentifier = 'field'): void
    {
        foreach ($fields ?? [] as $field) {
            $fieldObject = $context->getEntityByMultiple([
                'identifier' => $field['identifier'],
                'script' => $script
            ], $entityIdentifier);

            if (DataHandler::isEmpty($fieldObject)) {
                $fieldObject = new ScriptField();
            }

            $fieldObject->__set('identifier', $field['identifier']);
            $fieldObject->__set('placeholder', $field['placeholder'] ?? '');
            $fieldObject->__set('description', $field['description'] ?? '');
            $fieldObject->__set('type', $field['type']);
            $fieldObject->__set('value', $field['default'] ?? '');
            $fieldObject->__set('script', $script);
            $fieldObject->__set('config', DataHandler::getJsonEncode($field['config'] ?? []));
            $fieldObject->__set('settings', DataHandler::getJsonEncode($field['settings'] ?? []));
            $fieldObject->__set('classes', DataHandler::getJsonEncode($field['classes'] ?? []));

            $context->getEntityManager()->persist($fieldObject);
            $context->getEntityManager()->flush();
        }
    }
}
