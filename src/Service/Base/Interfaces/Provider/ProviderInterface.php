<?php

namespace Smug\Core\Service\Base\Interfaces\Provider;

/**
 * Interface ProviderInterface
 * @package Smug\Core\Service\Base\Interfaces\Provider
 */
interface ProviderInterface
{
    /**
     * @param array $config
     * @return array
     */
	public static function provide(array $config): array;
}
