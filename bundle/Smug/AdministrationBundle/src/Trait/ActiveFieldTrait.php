<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\AdministrationBundle\Service\Components\Factories\View\Field;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait ActiveFieldTrait
{
    public static function getField(string $fieldName): string
    {
        $fields = DataHandler::getJsonDecode(DataHandler::getFile(__DIR__ . '/../../../../activeFields.json'), true);

        foreach ($fields as $field) {
            if ($field['name'] === $fieldName) {
                return $field['class'] ?? Field::class;
            }
        }
        
        return Field::class;
    }
}