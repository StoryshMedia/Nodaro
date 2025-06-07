<?php

namespace Smug\Core\Struct\Base;

use Smug\Core\Entity\Base\Field\FieldDescription;
use Smug\Core\Struct\Base\StructInterface;

class BaseStruct implements StructInterface
{
	public function setValue($name, $value, FieldDescription $fieldDescription) {

		$this->__set($name, $value);
	}

	public function __set($name, $value)
	{
		$this->$name = $value;
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
