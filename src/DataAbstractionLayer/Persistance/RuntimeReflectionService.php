<?php
declare(strict_types=1);

namespace Smug\Core\DataAbstractionLayer\Persistance;

use Doctrine\Persistence\Mapping\RuntimeReflectionService as BaseRuntimeReflectionService;
use ReflectionProperty;

class RuntimeReflectionService extends BaseRuntimeReflectionService
{
    public function getAccessibleProperty(string $class, string $property): ?ReflectionProperty
    {
        $reflectionProperty = new RuntimeReflectionProperty($class, $property);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }
}