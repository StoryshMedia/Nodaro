<?php

namespace Smug\Core\Hydrator\Base\Field;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use ReflectionProperty;
use ReflectionAttribute;
use ReflectionClass;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

abstract class Field implements FieldInterface
{
    public static function buildFromType(ReflectionProperty $field): ?FieldInterface
    {
        $rc = new ReflectionClass(self::class);
        $type = self::getFieldType($field->getAttributes());

        if (DataHandler::isEmpty($type)) {
            return null;
        }

        if (DataHandler::isStringInString($type, 'field')) {
            $type = DataHandler::getReplaceString('Field', '', $type);
            $type = DataHandler::getReplaceString('field', '', $type);
        }

        $field = $rc->getNamespaceName() . '\\' . DataHandler::getFirstCapitalUpper($type) . 'Field';
        return new $field(); 
    }

    private static function getFieldType(array $attributes): string
    {
        /** @var ReflectionAttribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Column::class) {
                return $attribute->getArguments()['type'];
            }
            if ($attribute->getName() === ManyToOne::class) {
                return 'ManyToOne';
            }
        }

        return '';
    }
}