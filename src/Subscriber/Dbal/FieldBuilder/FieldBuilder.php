<?php

namespace Smug\Core\Subscriber\Dbal\FieldBuilder;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use ReflectionProperty;
use Smug\Core\Subscriber\Dbal\FieldBuilder\Type\Column as TypeColumn;
use Smug\Core\Subscriber\Dbal\FieldBuilder\Type\ManyToOne as TypeManyToOne;
use Smug\Core\Subscriber\Dbal\FieldBuilder\Type\TypeInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class FieldBuilder
{
	public static function createColumnFromReflection(ReflectionProperty $property, ClassMetadata $classMetaData): ClassMetadata
	{
		$type = self::getTypeBuilder($property);

		if (DataHandler::isEmpty($type)) {
			return $classMetaData;
		}

		return $type::transform($property, $classMetaData);
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
