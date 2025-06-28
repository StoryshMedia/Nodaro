<?php

namespace Smug\Core\Service\Base\Service\Provider;

use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;

class StructMappingProvider implements ProviderInterface
{
    public static function provide(array $config, EntitySerializer $serializer): array
    {
        return FileContentProvider::getSystemFileContent($config['fileName']);
    }
}
