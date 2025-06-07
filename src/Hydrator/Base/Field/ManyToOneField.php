<?php

namespace Smug\Core\Hydrator\Base\Field;

class ManyToOneField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        return $data[$key];
    }
}