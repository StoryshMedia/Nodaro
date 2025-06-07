<?php

namespace Smug\Core\Hydrator\Base\Field;

class UuidField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        return $data[$key] ?? null;
    }
}