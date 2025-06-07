<?php

namespace Smug\Core\Struct\Base;

use Smug\Core\Entity\Base\Field\FieldDescription;

interface StructInterface
{
	public function setValue($name, $value, FieldDescription $fieldDescription);

	public function __set($name, $value);

	public function __get($name);
}
