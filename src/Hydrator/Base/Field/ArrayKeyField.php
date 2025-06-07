<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ArrayKeyField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (DataHandler::isEmpty($data[$key])) {
            return null;
        }
    
        return $data[$key][$config['key']];
    }
}