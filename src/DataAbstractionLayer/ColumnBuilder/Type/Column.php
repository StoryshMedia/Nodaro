<?php

namespace Smug\Core\DataAbstractionLayer\ColumnBuilder\Type;

use Doctrine\DBAL\Schema\Column as BaseColumn;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\Type\TypeInterface;

class Column implements TypeInterface
{
	public static function transform(ReflectionProperty $property, Table $table): Table
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
