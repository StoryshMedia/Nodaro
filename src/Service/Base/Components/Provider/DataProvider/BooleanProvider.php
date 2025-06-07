<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

class BooleanProvider
{
    const BOLLEAN_VARS = ['true', 'false'];

    public static function getValuesAsBoolean(array $values)
    {
        foreach ($values as $key => $value) {
            if (in_array($value, self::BOLLEAN_VARS) && !is_numeric($value)) {
                $values[$key] = ($value === 'true' || $value === true);
            }
        }

        return $values;
    }
}
