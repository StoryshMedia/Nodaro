<?php

namespace Smug\Core\Hydrator\Base\Field;

class SubArrayField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        return self::hydrateSubArray($data, $key, $config);
    }

    public static function hydrateSubArray(array $data, string $key, array $config): array
    {
        $result = [];

        foreach ($data[$key] as $value) {
            $result[] = self::hydrate($value, $key, $config);
        }

        return $result;
    }
}