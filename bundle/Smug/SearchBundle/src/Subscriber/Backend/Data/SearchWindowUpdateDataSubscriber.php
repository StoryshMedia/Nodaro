<?php

namespace Smug\SearchBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SearchBundle\Entity\ListItem\ListItem;
use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;
use Smug\SearchBundle\Entity\MarketingItem\MarketingItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchWindowUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(SearchWindow::class)) {
            $requestData = $event->getContext()->getRequestData();

            $event->getContext()->addRepository('marketingItem', EntityGenerator::getGeneratedEntity(MarketingItem::class));
            $event->getContext()->addRepository('listItem', EntityGenerator::getGeneratedEntity(ListItem::class));
            
            $this->updateMarketingItems($event, $requestData['marketingItems']);
            $this->updateListItems($event, $requestData['listItems']);
        }
    }

    protected function updateMarketingItems(DataUpdatedEvent $event, array $marketingItems)
    {
        foreach ($marketingItems as $item) {
            $marketingItem = $event->getContext()->getEntityByIdentifier($item['id'], 'id', 'marketingItem');
            $marketingItem->__set('headline', $item['headline']);
            $marketingItem->__set('captionText', $item['captionText']);
            $marketingItem->__set('linkTarget', $item['linkTarget']);
            $marketingItem->__set('hidden', (bool)$item['hidden']);

            $event->getContext()->getEntityManager()->persist($marketingItem);
            $event->getContext()->getEntityManager()->flush();
        }
    }

    protected function updateListItems(DataUpdatedEvent $event, array $listItems)
    {
        foreach ($listItems as $item) {
            if (DataHandler::isArray($item['itemData'])) {
                $item['itemData'] = DataHandler::getJsonEncode($item['itemData']);
            }

            $listItem = $event->getContext()->getEntityByIdentifier($item['id'], 'id', 'listItem');
            $listItem->__set('itemData', $item['itemData']);
            $listItem->__set('detailLink', $item['detailLink']);
            $listItem->__set('hidden', (bool)$item['hidden']);

            $event->getContext()->getEntityManager()->persist($listItem);
            $event->getContext()->getEntityManager()->flush();
        }
    }
}