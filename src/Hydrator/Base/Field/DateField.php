<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use \DateTime;

class DateField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (DataHandler::isArray($data[$key])) {
            return new DateTime($data[$key]['date']);
        }

        if (!DataHandler::isObject($data[$key])) {
            return new DateTime($data[$key]);
        }

        return $data[$key];
    }
}