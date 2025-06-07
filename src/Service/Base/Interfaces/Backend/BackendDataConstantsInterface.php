<?php

namespace Smug\Core\Service\Base\Interfaces\Backend;

interface BackendDataConstantsInterface
{
	public static function getListConstants(): array;

	public static function getDetailConstants(): array;

	public static function getAddConstants(): array;
}
