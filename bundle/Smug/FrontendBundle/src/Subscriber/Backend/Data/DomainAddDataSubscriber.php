<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Seo\Seo;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainAddDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_CREATED => 'onDataCreated'
        ];
    }

    public function onDataCreated(DataCreatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Domain::class)) {
            $data = $event->getData();
            $siteClass = EntityGenerator::getGeneratedEntity(Site::class);
            $site = new $siteClass();

            $site->__set('title', 'Root');
            $site->__set('domain', $data);
            $site->__set('hidden', true);
            $site->__set('hiddenInMenu', true);
            $site->__set('slug', '/');
            $site->__set('canonicalLink', '');
            $site->__set('seoKeywords', '');
            $site->__set('seoDescription', '');
            $site->__set('seoTitle', '');
            $site->__set('parentId', '');
            $site->__set('seoData', DataHandler::getJsonEncode([]));
            $site->__set('noFollow', false);
            $site->__set('noIndex', false);
            $site->__set('rootPage', true);
            $site->__set('siteStyles', DataHandler::getJsonEncode([]));

            $event->getContext()->getEntityManager()->persist($site);
            $event->getContext()->getEntityManager()->flush();

            $seoClass = EntityGenerator::getGeneratedEntity(Seo::class);
            $seoData = new $seoClass();
            $seoData->__set('title', '');
            $seoData->__set('domain', $data);

            $event->getContext()->getEntityManager()->persist($seoData);
            $event->getContext()->getEntityManager()->flush();

            $data->__set('seo', $seoData);
            
            $event->getContext()->getEntityManager()->persist($data);
            $event->getContext()->getEntityManager()->flush();

            $event->setData($data->toArray());
        }
    }
}