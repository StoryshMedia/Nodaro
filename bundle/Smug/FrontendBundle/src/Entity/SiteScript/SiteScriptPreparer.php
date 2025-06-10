<?php

namespace Smug\FrontendBundle\Entity\SiteScript;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class SiteScriptPreparer extends QueryMapper
{
    public function prepare(array $item, array $mapValues): array
    {
        return DataHandler::mergeArray($item, $mapValues);
    }
}
