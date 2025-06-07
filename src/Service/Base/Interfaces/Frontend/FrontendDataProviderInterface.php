<?php

namespace Smug\Core\Service\Base\Interfaces\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface FrtontendDataProviderInterface
 * @package Smug\Core\Service\Base\Interfaces\Frtontend
 */
interface FrontendDataProviderInterface
{
    /**
     * @param string $slug
     * @param EntityManagerInterface $em
     * @param bool $list
     * @param array $additional
     * @return array
     */
	public static function provide(string $slug, EntityManagerInterface $em, bool $list = false, array $additional = []): array;

    /**
     * @param array $data
     * @return string
     */
    public static function getBreadcrumb(array $data): string;
}
