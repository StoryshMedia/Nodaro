<?php

namespace Smug\Core\DataAbstractionLayer\ColumnBuilder\Type;

use Doctrine\DBAL\Schema\Table;
use ReflectionProperty;

interface TypeInterface
{
	/**
	 * @param ReflectionProperty $property
	 * @param Table $table
	 * @return Table
	 */
	public static function transform(ReflectionProperty $property, Table $table): Table;
}
