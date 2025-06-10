<?php

namespace Smug\FrontendBundle\Subscriber\Backend\List;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataModelLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
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
            SystemEvents::DATA_MODEL_LOADED => 'onDataLoaded'
        ];
    }

    public function onDataLoaded(DataModelLoadedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Site::class)) {
            $data = $event->getData();
            $data = $this->renderSite($data);
            $data['additionalJavascripts'] = $this->getSiteJavaScripts($data);
            
            $event->setData($data);
        }
    }

    public function getSiteJavaScripts(array $site): array
    {
        $javaScripts = [];

        foreach ($site['contentItems'] as $contentItem) {
            $javaScripts = DataHandler::mergeArray(
                $javaScripts,
                DataHandler::getJsonDecode($contentItem['module']['module']['scripts'], true)
            );
        }
        
        return $javaScripts;
    }

    protected function renderSite(array $site): array
    {
        $site = $this->renderer->render($site);

        return $site;
    }
}