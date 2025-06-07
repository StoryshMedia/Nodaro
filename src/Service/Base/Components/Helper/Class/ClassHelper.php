<?php

namespace Smug\Core\Service\Base\Components\Helper\Class;

use ReflectionClass;
use ReflectionProperty;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ClassHelper
{
    public static function getClassProperty(ReflectionClass $class, string $propertyName): ?ReflectionProperty
    {
        foreach ($class->getProperties() as $property) {
            if ($property->getName() === $propertyName) {
                return $property;
            }
        }

        return null;
    }

    public static function getMappingProperty(ReflectionClass $class, string $propertyName): ?ReflectionProperty
    {
        foreach ($class->getProperties() as $property) {
            if (
                $property->getName() !== $propertyName &&
                !DataHandler::isEmpty(
                    self::getAttributeArguments($property, 'targetEntity')
                )
            ) {
                return $property;
            }
        }

        return null;
    }

    public static function getAttributeArguments(ReflectionProperty $property, string $attributeName): ?array
    {
        foreach ($property->getAttributes() as $attribute) {
            if (DataHandler::doesKeyExists($attributeName, $attribute->getArguments())) {
                return $attribute->getArguments();
            }
        }

        return null;
    }
}
