<?php

namespace Smug\Core\Service\Base\Interfaces\Seo;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Interface SeoDataProviderInterface
 * @package Smug\Core\Service\Base\Interfaces\Seo
 */
interface SeoDataProviderInterface
{
    /**
     * @param array $data
     * @param bool $list
     * @return array
     */
	public static function provide(array $data, bool $list = false): array;


    /**
     * @param array $data
     * @param string $description
     * @return string
     */
    public static function getSchemaData(array $data, string $description): string;
}
