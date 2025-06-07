<?php

namespace Smug\Core\Subscriber\Dbal\FieldBuilder\Type;

use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionProperty;

interface TypeInterface
{
	/**
	 * @param ReflectionProperty $property
	 * @param ReflectionProperty $classMetaData
	 * @return ClassMetadata
	 */
	public static function transform(ReflectionProperty $property, ClassMetadata $classMetaData): ClassMetadata;
}
