<?php

namespace Smug\Core\Subscriber\Dbal\FieldBuilder\Type;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\Table as MappingTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne as BaseManyToOne;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Smug\Core\Subscriber\Dbal\FieldBuilder\Type\TypeInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ManyToOne implements TypeInterface
{
	public static function transform(ReflectionProperty $property, ClassMetadataInfo $classMetaData): ClassMetadata
	{
        if (DataHandler::doesKeyExists($property->getName(), $classMetaData->associationMappings)) {
            return $classMetaData;
        }

		$attributes = $property->getAttributes(BaseManyToOne::class);

        $manyToOne = $attributes[0]->newInstance();
        $targetEntity = $manyToOne->targetEntity;

        $joinColumnAttributes = $property->getAttributes(JoinColumn::class);
        $joinColumn = $joinColumnAttributes[0]->newInstance() ?? null;

        $columnName = $joinColumn->name ?? strtolower($property->getName() . "_id");
        $referencedColumn = $joinColumn->referencedColumnName ?? "id";

        $classMetaData->addInheritedAssociationMapping(self::_validateAndCompleteAssociationMapping(
            [
                'fieldName' => $property->getName(),
                'targetEntity' => $targetEntity,
                'type' => ClassMetadataInfo::MANY_TO_ONE,
                'joinColumns'  => [
                    [
                        'name' => $columnName,
                        'referencedColumnName' => $referencedColumn,
                        'nullable' => $joinColumn->nullable ?? true,
                        'onDelete' => $joinColumn->onDelete ?? null,
                    ],
                ],
            ],
            $property->getName(),
            $classMetaData->name
        ));

        return $classMetaData;
	}

    protected static function getReferenceTableName(ReflectionClass $targetEntity): string
    {
        $table = $targetEntity->getAttributes(MappingTable::class);

        if (!DataHandler::isEmpty($table)) {
            $table[0]->getArguments()[0];

            return $table[0]->getArguments()[0];
        }
        
        return DataHandler::getLowerString($targetEntity->getShortName());
    }

    protected static function _validateAndCompleteAssociationMapping(array $mapping, string $name, string $namespace): array
    {
        if (! isset($mapping['mappedBy'])) {
            $mapping['mappedBy'] = null;
        }

        if (! isset($mapping['inversedBy'])) {
            $mapping['inversedBy'] = null;
        }

        $mapping['isOwningSide'] = true; // assume owning side until we hit mappedBy

        if (empty($mapping['indexBy'])) {
            unset($mapping['indexBy']);
        }

        // If targetEntity is unqualified, assume it is in the same namespace as
        // the sourceEntity.
        $mapping['sourceEntity'] = $name;
        $mapping = self::validateAndCompleteTypedAssociationMapping($mapping);

        if (isset($mapping['targetEntity'])) {
            $mapping['targetEntity'] = self::fullyQualifiedClassName($mapping['targetEntity'], $namespace);
            $mapping['targetEntity'] = ltrim($mapping['targetEntity'], '\\');
        }

        // Mandatory and optional attributes for either side
        if (! $mapping['mappedBy']) {
            if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                if (isset($mapping['joinTable']['name']) && $mapping['joinTable']['name'][0] === '`') {
                    $mapping['joinTable']['name']   = trim($mapping['joinTable']['name'], '`');
                    $mapping['joinTable']['quoted'] = true;
                }
            }
        } else {
            $mapping['isOwningSide'] = false;
        }

        // Fetch mode. Default fetch mode to LAZY, if not set.
        if (! isset($mapping['fetch'])) {
            $mapping['fetch'] = ClassMetadataInfo::FETCH_LAZY;
        }

        // Cascades
        $cascades = isset($mapping['cascade']) ? array_map('strtolower', $mapping['cascade']) : [];

        $allCascades = ['remove', 'persist', 'refresh', 'merge', 'detach'];
        if (in_array('all', $cascades, true)) {
            $cascades = $allCascades;
        } 

        $mapping['cascade'] = $cascades;
        $mapping['isCascadeRemove'] = in_array('remove', $cascades, true);
        $mapping['isCascadePersist'] = in_array('persist', $cascades, true);
        $mapping['isCascadeRefresh'] = in_array('refresh', $cascades, true);
        $mapping['isCascadeMerge'] = in_array('merge', $cascades, true);
        $mapping['isCascadeDetach'] = in_array('detach', $cascades, true);

        foreach ($mapping['joinColumns'] as &$joinColumn) {
            if ($joinColumn['name'][0] === '`') {
                $joinColumn['name'] = trim($joinColumn['name'], '`');
                $joinColumn['quoted'] = true;
            }

            if ($joinColumn['referencedColumnName'][0] === '`') {
                $joinColumn['referencedColumnName'] = trim($joinColumn['referencedColumnName'], '`');
                $joinColumn['quoted'] = true;
            }

            $mapping['sourceToTargetKeyColumns'][$joinColumn['name']] = $joinColumn['referencedColumnName'];
            $mapping['joinColumnFieldNames'][$joinColumn['name']] = $joinColumn['fieldName'] ?? $joinColumn['name'];
        }

        $mapping['targetToSourceKeyColumns'] = array_flip($mapping['sourceToTargetKeyColumns']);
        
        return $mapping;
    }

    public static function fullyQualifiedClassName(string $className, string $namespace)
    {
        if (empty($className)) {
            return $className;
        }

        if (! str_contains($className, '\\') && $namespace) {
            return $namespace . '\\' . $className;
        }

        return $className;
    }

    protected static function validateAndCompleteTypedAssociationMapping(array $mapping): array
    {
        $type = ClassMetadataInfo::MANY_TO_ONE;

        if (! isset($mapping['targetEntity']) && $type instanceof ReflectionNamedType) {
            $mapping['targetEntity'] = $type->getName();
        }

        return $mapping;
    }
}
