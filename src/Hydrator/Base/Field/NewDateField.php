<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use \DateTime;

class NewDateField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (
            !(
                DataHandler::doesKeyExists('id', $data) &&
                DataHandler::isEmpty($data['id'])
            ) &&
            DataHandler::doesKeyExists($key, $data)
        ) {
            if (DataHandler::isArray($data[$key])) {
                return new DateTime($data[$key]['date']);
            }

            if (!DataHandler::isObject($data[$key])) {
                return new DateTime($data[$key]);
            }
        }

        return new DateTime();
    }
}