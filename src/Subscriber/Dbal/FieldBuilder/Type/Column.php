<?php

namespace Smug\Core\Subscriber\Dbal\FieldBuilder\Type;

use Doctrine\DBAL\Schema\Column as BaseColumn;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionProperty;
use Smug\Core\Subscriber\Dbal\FieldBuilder\Type\TypeInterface;

class Column implements TypeInterface
{
	public static function transform(ReflectionProperty $property, ClassMetadata $classMetaData): ClassMetadata
	{
		$attributes = $property->getAttributes(BaseColumn::class);

		$columnMetadata = $attributes[0]->newInstance();

        $table->addColumn(
            $property->getName(),
            Type::getType($columnMetadata->type),
            [
                'length' => $columnMetadata->length ?? null,
                'notnull' => !$columnMetadata->nullable,
            ]
        );

        return $table;
	}
}
