<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataDeletedEvent;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteScriptDeleteDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_DELETED => 'onDataDeleted'
        ];
    }

    public function onDataDeleted(DataDeletedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(SiteScript::class)) {
            $data = $event->getData();
            $count = 0;

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
            
            foreach ($siteScripts as $siteScript) {
                $siteScript->__set('position', $count);
    
                $event->getContext()->getEntityManager()->persist($siteScript);
                $event->getContext()->getEntityManager()->flush();
    
                $count++;
            }


            $event->setData($data);
        }
    }
}