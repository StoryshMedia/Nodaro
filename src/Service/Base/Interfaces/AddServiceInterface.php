<?php

namespace Smug\Core\Service\Base\Interfaces;

use Doctrine\DBAL\ConnectionException;
use Smug\Core\Context\Context;

/**
 * Interface AddServiceInterface
 * @package Smug\Core\Service\Base\Interfaces
 */
interface AddServiceInterface
{
	/**
	 * @param Context $context
	 * @param bool $import
	 * @return array
	 * @throws ConnectionException
	 */
	public function add(Context $context, $import = false): array;
}
