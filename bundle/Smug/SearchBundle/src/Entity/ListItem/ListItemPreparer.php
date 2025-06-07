<?php

namespace Smug\SearchBundle\Entity\ListItem;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class ListItemPreparer extends QueryMapper
{
    public function prepare(array $item, array $mapValues): array
    {
        if (DataHandler::isArray($item['itemData'])) {
            $item['itemData'] = DataHandler::getJsonEncode($item['itemData']);
        }

        return DataHandler::mergeArray($item, $mapValues);
    }
}
