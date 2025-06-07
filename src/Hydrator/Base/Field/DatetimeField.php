<?php

namespace Smug\Core\Hydrator\Base\Field;

use DateTime;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;

class DatetimeField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (!DataHandler::doesKeyExists($key, $data)) {
            return null;
        }
        
        if (DataHandler::isString($data[$key]) && !DataHandler::isEmpty($data[$key])) {
            return TimeHandler::getNewDateObject($data[$key]);
        }

        if (DataHandler::isInstanceOf($data[$key], DateTime::class)) {
            return $data[$key];    
        }

        if (DataHandler::isArray($data[$key])) {
            return TimeHandler::getNewDateObject($data[$key]['date']);    
        }

        return TimeHandler::getNewDateObject();
    }
}