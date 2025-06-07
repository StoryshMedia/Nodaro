<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class IdentifierField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if ($data[$key] === '' || !DataHandler::doesKeyExists($key, $data)) {
            return null;
        }

        return $data[$key];
    }
}