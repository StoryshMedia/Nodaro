<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;

class ArrayProvider
{
    public static function getObjectsAsArray($objects, ?EntitySerializer $serializer = null, array $disAllowedFields = [], bool $getChildren = true, array $restrictions = [], array $groups = ['public']): array
    {
        $return = [];
        
        if (DataHandler::isEmpty($objects)) {
            return $return;
        }
        foreach ($objects as $object) {
                if (DataHandler::doesMethodExist($object, 'toArray')) {
                    $array = $object->toArray($disAllowedFields, $getChildren, $restrictions);
                    if (DataHandler::isEmpty($array)) {
                        continue;
                    }
                    $return[] = $array;
                } else {
                    if (DataHandler::isEmpty($serializer)) {
                        continue;
                    }
                    
                    $array = $serializer->serialize($object, $groups);

                    if (DataHandler::isEmpty($array)) {
                        continue;
                    }
                    $return[] = $array;
                }
                
            }
        try {
            
        } catch (\Throwable $e) {
            dd($e);
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
