<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ArrayProvider
{
    public static function getObjectsAsArray($objects, array $disAllowedFields = [], bool $getChildren = true, array $restrictions = []): array
    {
        $return = [];
        
        if (DataHandler::isEmpty($objects)) {
            return $return;
        }

        /** @var BaseModel $object */
        foreach ($objects as $object) {
            $array = $object->toArray($disAllowedFields, $getChildren, $restrictions);
            if (DataHandler::isEmpty($array)) {
                continue;
            }
            $return[] = $array;
        }

        try {
        } catch (\Throwable $e) {
        }

        return $return;
    }

    public static function getObjectsFieldsAsArray($objects, array $fields, array $disAllowedFields = [], bool $getChildren = true, array $restrictions = []): array
    {
        $return = [];
        
        if (DataHandler::isEmpty($objects)) {
            return $return;
        }

        /** @var BaseModel $object */
        foreach ($objects as $object) {
            $item = [];

            foreach ($fields as $field) {
                $item[$field] = $object->__get($field);
            }
            
            $return[] = $item;
        }

        try {
        } catch (\Throwable $e) {
        }

        return $return;
    }

    public static function getObjectFromChildItem($objects, string $field, string $match) : ?BaseModel
    {
        /** @var BaseModel $object */
        foreach ($objects as $object) {
            if ($object->__get($field) === $match) {
                return $object;
            }
        }

        return null;
    }
}
