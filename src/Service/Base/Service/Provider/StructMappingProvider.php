<?php

namespace Smug\Core\Service\Base\Service\Provider;

use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;

/**
 * Class StructMappingProvider
 * @package Smug\Core\Service\Base\Service\Provider
 */
class StructMappingProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function provide(array $config): array
    {
        return FileContentProvider::getSystemFileContent($config['fileName']);
    }
}
