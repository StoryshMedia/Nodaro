<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataPreCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteScriptPreAddDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_BEGIN_CREATE => 'onDataPreCreaed'
        ];
    }

    public function onDataPreCreaed(DataPreCreatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(SiteScript::class)) {
            $data = $event->getData();

            $siteScripts = $event->getContext()->getByRestrictions(
                [
                    [
                        'condition' => 'site',
                        'value' => $data['site']['id']
                    ],
                    [
                        'condition' => 'area',
                        'value' => $data['area']
                    ]
                ]
            );

            $event->getContext()->updateData('position', DataHandler::getArrayLength($siteScripts));
        }
    }
}