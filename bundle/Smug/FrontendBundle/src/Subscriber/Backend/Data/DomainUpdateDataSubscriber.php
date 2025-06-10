<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Domain::class)) {
            $requestData = $event->getContext()->getRequestData();

            $seo = $event->getData()->__get('seo');

            $seo->__set('title', $requestData['seo']['title']);
            $event->getContext()->getEntityManager()->persist($seo);
            $event->getContext()->getEntityManager()->flush();
        }
    }
}