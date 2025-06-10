<?php

namespace Smug\FrontendBundle\Entity\ContentItem;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class ContentItemPreparer extends QueryMapper
{
    public function prepare(array $item, array $mapValues): array
    {
        if (DataHandler::isArray($item['additionalClasses'])) {
            $item['additionalClasses'] = DataHandler::getJsonEncode($item['additionalClasses']);
        }

        if (
            !DataHandler::doesKeyExists('settings', $item) ||
            !DataHandler::doesKeyExists('template', $item['settings'])
        ) {
            if (DataHandler::isArray($item['module']['module']['template'])) {
                $item['settings']['template'] = $item['module']['module']['template'];
            } else {
                $item['settings']['template'] = DataHandler::getJsonDecode($item['module']['module']['template'], true);
            }
        }
        if (DataHandler::isArray($item['settings']['template']['classes'] ?? null)) {
            $item['templateClasses'] = DataHandler::getJsonEncode($item['settings']['template']['classes']);
        } else {
            $item['templateClasses'] = DataHandler::getJsonEncode([]);
        }

        if (DataHandler::isEmpty($item['title'] ?? '')) {
            $item['title'] = $item['module']['module']['title'];
        }
        
        return DataHandler::mergeArray($item, $mapValues);
    }
}
