<?php

namespace Smug\SearchBundle\Subscriber\Frontend;

use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Frontend\Site\SiteLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Smug\FrontendBundle\Service\Frontend\Renderer\SiteRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteDataLoadedSubscriber implements EventSubscriberInterface
{
    protected SiteRenderer $renderer;

    public function __construct(SiteRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FrontendEvents::FRONTEND_PAGE_LOADED => 'onDataLoaded'
        ];
    }

    public function onDataLoaded(SiteLoadedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Site::class)) {
            $data = $event->getData();
            if (!DataHandler::isEmpty($data['site']->__get('domain')->__get('searchWindow'))) {
                $data['additional']['searchWindowData'] = $data['site']->__get('domain')->__get('searchWindow')->toArray();
            }
            $event->setData($data);
        }
    }
}