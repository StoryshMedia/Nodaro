<?php

namespace Smug\FrontendBundle\Service\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ContentItemFactory
{
    public static function getContentItemAndKey(string $identifier, array $items = []): bool|array
    {
        $position = DataHandler::getPositionInMultiDimensionalArray($identifier, $items, 'identifier');
        if (!$position) {
            return false;
        }

        return [
            'key' => $position,
            'item' => $items[$position]
        ];
    }

    public static function getContentItemFieldAndKey(string $identifier, array $item): bool|array
    {
        $position = DataHandler::getPositionInMultiDimensionalArray($identifier, $item['fields'] ?? [], 'identifier');
        if (!$position) {
            return false;
        }

        return [
            'key' => $position,
            'field' => $item['fields'][$position]
        ];
    }
}