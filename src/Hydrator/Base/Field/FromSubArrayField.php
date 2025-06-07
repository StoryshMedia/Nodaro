<?php

namespace Smug\Core\Hydrator\Base\Field;

class FromSubArrayField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        return $data[$config['parentKey']][$key];
    }
}