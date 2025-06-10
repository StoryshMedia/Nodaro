<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Frontend\Site\SeoDataLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SeoDataBuilder
{
    public static function getSeoData(array $data, ?EventDispatcherInterface $dispatcher = null): array
    {
        $site = $data['site'];
        $siteArray = (DataHandler::isInstanceOf($site, EntityGenerator::getGeneratedEntity(Site::class))) ? $site->toArray() : $site;

        $seoData = [
            'domain' => self::getDomainSeoData($site->__get('domain')),
            'noIndex' => $siteArray['noIndex'],
            'noFollow' => $siteArray['noFollow'],
            'seoData' => DataHandler::getJsonEncode($siteArray['seoData']),
            'seoDescription' => $siteArray['seoDescription'],
            'seoTitle' => $siteArray['seoTitle'],
            'seoImage' => [],
            'seoKeywords' => $siteArray['seoKeywords'],
            'canonicalLink' => $siteArray['canonicalLink']
        ];

        $event = new SeoDataLoadedEvent(
            $seoData
        );

        if ($dispatcher) {
            $dispatcher->dispatch(
                $event,
                FrontendEvents::FRONTEND_PAGE_SEO_DATA_LOADED
            );
        }

        return $event->getData();
    }

    private static function getDomainSeoData($domain) : array {
        $seoData = $domain->__get('seo');
        $images = ArrayProvider::getObjectsAsArray($seoData->__get('images'));
        
        return [
            'title' => $seoData->__get('title'),
            'image' => DataHandler::getFirstArrayElement($images)['media'] ?? []
        ];
    }
}
