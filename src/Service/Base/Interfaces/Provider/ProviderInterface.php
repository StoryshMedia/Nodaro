<?php

namespace Smug\Core\Service\Base\Interfaces\Provider;

use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;

interface ProviderInterface
{
	public static function provide(array $config, EntitySerializer $serializer): array;
}
