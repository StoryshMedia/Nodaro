<?php

namespace Smug\Core\Service\Base\Interfaces\PrepareService;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Interface PrepareServiceInterface
 * @package Smug\Core\Service\Base\Interfaces\PrepareService
 */
interface PrepareServiceInterface
{
	/**
	 * @param BaseModel $model
	 * @param array $config
	 * @return array
	 */
	public static function prepare(BaseModel $model, array $config): array;
}
