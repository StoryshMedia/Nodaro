<?php

namespace Smug\Core\Service\Base\Interfaces\Backend;

/**
 * Interface BackendDataConstantsInterface
 * @package Smug\Core\Service\Base\Interfaces\Backend
 */
interface BackendDataStructureInterface
{
    /**
     * @return array
     */
	public static function getStructure(): array;
}
