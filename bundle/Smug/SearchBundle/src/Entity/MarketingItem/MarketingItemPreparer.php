<?php

namespace Smug\SearchBundle\Entity\MarketingItem;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class MarketingItemPreparer extends QueryMapper
{
    public function prepare(array $item, array $mapValues): array
    {
        return DataHandler::mergeArray($item, $mapValues);
    }
}
