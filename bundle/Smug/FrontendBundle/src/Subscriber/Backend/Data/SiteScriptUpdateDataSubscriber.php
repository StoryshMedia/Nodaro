<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\FrontendBundle\Entity\Script\Script;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteScriptUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Script::class)) {
            $requestFields = $event->getContext()->getRequestData()['fields'];
            foreach ($event->getData()->__get('fields') as $field) {
                $field->__set('value', self::getFieldValue($field->__get('identifier'), $requestFields));

                $event->getContext()->getEntityManager()->persist($field);
                $event->getContext()->getEntityManager()->flush();
            }
        }
    }

    protected static function getFieldValue(string $identifier, array $requestFields): mixed
    {
        foreach ($requestFields as $field) {
            if ($field['identifier'] === $identifier) {
                return $field['value'];
            }
        }

        return '';
    }
}