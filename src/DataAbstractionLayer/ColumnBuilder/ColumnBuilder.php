<?php

namespace Smug\Core\DataAbstractionLayer\ColumnBuilder;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\Type\Column as TypeColumn;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\Type\ManyToOne as TypeManyToOne;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\Type\TypeInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ColumnBuilder
{
	public static function createColumnFromReflection(ReflectionProperty $property, Table $table): Table
	{
		$type = self::getTypeBuilder($property);

		if (DataHandler::isEmpty($type)) {
			return $table;
		}

		return $type::transform($property, $table);
	}

	protected static function getTypeBuilder(ReflectionProperty $property): ?TypeInterface
	{
		$attributes = $property->getAttributes(Column::class);

		if (!DataHandler::isEmpty($attributes)) {
			return new TypeColumn();
		}

		$attributes = $property->getAttributes(ManyToOne::class);
		
		if (!DataHandler::isEmpty($attributes)) {
			return new TypeManyToOne();
		}

		return null;
	}
}
