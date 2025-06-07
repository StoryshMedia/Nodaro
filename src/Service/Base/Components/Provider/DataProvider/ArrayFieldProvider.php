<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

/**
 * Class ArrayFieldProvider
 * @package Smug\Core\Service\Base\Components\Provider\DataProvider
 */
class ArrayFieldProvider
{
    /**
     * @param array $data
     * @param array $additional
     * @return array
     */
    public static function addAdditionalFields(array $data, array $additional)
    {
        foreach ($additional as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
