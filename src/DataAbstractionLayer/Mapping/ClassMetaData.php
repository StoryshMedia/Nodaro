<?php
declare(strict_types=1);

namespace Smug\Core\DataAbstractionLayer\Mapping;

use Doctrine\Instantiator\Instantiator;
use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetaData;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ReflectionEmbeddedProperty;
use Doctrine\ORM\Mapping\ReflectionEnumProperty;
use Doctrine\ORM\Mapping\ReflectionReadonlyProperty;
use Doctrine\ORM\Mapping\TypedFieldMapper;
use Doctrine\Persistence\Mapping\ReflectionService;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\SchemaExtensionBuilder;

class ClassMetaData extends BaseClassMetaData
{
    public function __construct($entityName, ?NamingStrategy $namingStrategy = null, ?TypedFieldMapper $typedFieldMapper = null)
    {
        parent::__construct($entityName, $namingStrategy, $typedFieldMapper);
    }

    public function wakeupReflection($reflService)
    {
        // Restore ReflectionClass and properties
        $this->reflClass = $reflService->getClass($this->name);
        $instantiator = new Instantiator();

        $parentReflFields = [];

        foreach ($this->embeddedClasses as $property => $embeddedClass) {
            if (isset($embeddedClass['declaredField'])) {
                assert($embeddedClass['originalField'] !== null);
                $childProperty = $this->getAccessibleProperty(
                    $reflService,
                    $this->embeddedClasses[$embeddedClass['declaredField']]['class'],
                    $embeddedClass['originalField']
                );
                assert($childProperty !== null);
                $parentReflFields[$property] = new ReflectionEmbeddedProperty(
                    $parentReflFields[$embeddedClass['declaredField']],
                    $childProperty,
                    $this->embeddedClasses[$embeddedClass['declaredField']]['class']
                );

                continue;
            }

            $fieldRefl = $this->getAccessibleProperty(
                $reflService,
                $embeddedClass['declared'] ?? $this->name,
                $property
            );

            $parentReflFields[$property] = $fieldRefl;
            $this->reflFields[$property] = $fieldRefl;
        }

        foreach ($this->fieldMappings as $field => $mapping) {
            if (isset($mapping['declaredField']) && isset($parentReflFields[$mapping['declaredField']])) {
                $childProperty = $this->getAccessibleProperty($reflService, $mapping['originalClass'], $mapping['originalField']);
                assert($childProperty !== null);

                if (isset($mapping['enumType'])) {
                    $childProperty = new ReflectionEnumProperty(
                        $childProperty,
                        $mapping['enumType']
                    );
                }

                $this->reflFields[$field] = new ReflectionEmbeddedProperty(
                    $parentReflFields[$mapping['declaredField']],
                    $childProperty,
                    $mapping['originalClass']
                );
                continue;
            }

            $this->reflFields[$field] = isset($mapping['declared'])
                ? $this->getAccessibleProperty($reflService, $mapping['declared'], $field)
                : $this->getAccessibleProperty($reflService, $this->name, $field);

            if (isset($mapping['enumType']) && $this->reflFields[$field] !== null) {
                $this->reflFields[$field] = new ReflectionEnumProperty(
                    $this->reflFields[$field],
                    $mapping['enumType']
                );
            }
        }

        foreach ($this->associationMappings as $field => $mapping) {
            $this->reflFields[$field] = isset($mapping['declared'])
                ? $this->getAccessibleProperty($reflService, $mapping['declared'], $field)
                : $this->getAccessibleProperty($reflService, $this->name, $field);
        }
    }

    /** @psalm-param class-string $class */
    private function getAccessibleProperty(ReflectionService $reflService, string $class, string $field): ?ReflectionProperty
    {
        try {
            $reflectionProperty = $reflService->getAccessibleProperty($class, $field);
            if ($reflectionProperty !== null && PHP_VERSION_ID >= 80100 && $reflectionProperty->isReadOnly()) {
                $declaringClass = $reflectionProperty->class;
                if ($declaringClass !== $class) {
                    $reflectionProperty = $reflService->getAccessibleProperty($declaringClass, $field);
                }
    
                if ($reflectionProperty !== null) {
                    $reflectionProperty = new ReflectionReadonlyProperty($reflectionProperty);
                }
            }
        } catch (\Throwable $e) {
            $reflectionProperty = SchemaExtensionBuilder::getSchemaExtensionForClassAndName($class, $field);
        }

        return $reflectionProperty;
    }
}