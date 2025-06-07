<?php

namespace Smug\SearchBundle\Entity\SearchWindow;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class SearchWindowPreparer extends QueryMapper
{
    public function prepare(array $item, array $mapValues): array
    {
        if (DataHandler::isArray($item['detailPages'])) {
            $item['detailPages'] = DataHandler::getJsonEncode($item['detailPages']);
        }

        return DataHandler::mergeArray($item, $mapValues);
    }
}
