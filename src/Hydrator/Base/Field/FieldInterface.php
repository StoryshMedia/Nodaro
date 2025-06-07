<?php

namespace Smug\Core\Hydrator\Base\Field;

use ReflectionProperty;

interface FieldInterface
{
	public static function buildFromType(ReflectionProperty $field): ?FieldInterface;

	public static function hydrate(array $data, string $key, array $config = []): mixed;
}
