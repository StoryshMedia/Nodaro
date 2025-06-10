<?php

namespace Smug\FrontendBundle\Controller\Frontend\Api\Base;

use Doctrine\ORM\NoResultException;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Frontend\Site\SiteContentLoadedEvent;
use Smug\Core\Events\Frontend\Site\SiteLoadedEvent;
use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Smug\FrontendBundle\Service\Frontend\Renderer\ScriptBuilder;
use Smug\FrontendBundle\Service\Frontend\Renderer\SeoDataBuilder;
use Smug\FrontendBundle\Service\Frontend\Renderer\SiteContentBuilder;
use Smug\FrontendBundle\Service\Frontend\Renderer\StyleBuilder;

class FeBaseController extends BaseController
{
    public function sendErrorMail(array $data)
    {
        $this->sendMail->sendHtmlMail(
            '@SmugFrontend/email/error/html/index.html.twig',
            [
                'from' => 'admin@storysh.de',
                'subject' => 'Auf einer Seite ist ein Fehler aufgetreten',
                'recipients' => [
                    [
                        'email' => 'admin@storysh.de'
                    ]
                ]
            ],
            [
                'data' => $data
            ]
        );
    }

    public function getSiteContent(Request $request): ?array
    {
        $this->context->buildFromRequest($request);
        $site = $this->getSiteData($request);

        if (DataHandler::isEmpty($site)) {
            return null;
        }

        $styles = StyleBuilder::getSiteStyles($site);
        $contentItems = SiteContentBuilder::getContentItems(
            $site['site'],
            $site['additionalData'],
            $this->dispatcher,
            $this->context
        );
        
        $siteData = [
            'seo' => SeoDataBuilder::getSeoData($site, $this->dispatcher),
            'scripts' => ScriptBuilder::getSiteScripts($site, $this->dispatcher),
            'template' => $site['site']->__get('domain')->__get('templateString'),
            'styles' => $styles,
            'baseSlug' => $site['slug'],
            'contentItems' => $contentItems,
            'loadedItemsInfo' => SiteContentBuilder::getLoadedContentItemsInfo($contentItems),
            'currentSlug' => $request->getPathInfo()
        ];

        return $this->dispatchData(
            $siteData,
            $this->context,
            SiteContentLoadedEvent::class,
            EntityGenerator::getGeneratedEntity(Site::class),
            FrontendEvents::FRONTEND_PAGE_CONTENT_LOADED
        );
    }

    public function getSiteData(Request $request) : ?array
    {
        $urlParts = $this->getUrlParts($request);
        $slugParts = $this->getSlugParts($request);
        $slug = DataHandler::isEmpty($slugParts['site']) ? '/' : $slugParts['site'];

        if (!DataHandler::stringStartsWith($slug, '/')) {
            $slug = '/' . $slug;
        }
        
        try {
            $qb = $this->em->createQueryBuilder();
            $site = $qb
                ->select('s')
                ->from(EntityGenerator::getGeneratedEntity(Site::class), 's')
                ->innerJoin('s.domain', 'd')
                ->where('s.slug = :slug')
                ->andWhere('d.url = :domain')
                ->setParameter('slug', $slug)
                ->setParameter('domain', $urlParts['scheme'] . '://' . $urlParts['host'])
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            return null;
        }
        
        $site = $this->dispatchData(
            [
                'site' => $site,
                'slug' => $slugParts['site'],
                'additionalData' => $slugParts['additional']
            ],
            $this->context,
            SiteLoadedEvent::class,
            EntityGenerator::getGeneratedEntity(Site::class),
            FrontendEvents::FRONTEND_PAGE_LOADED
        );

        return $site;
    }

    private function getUrlParts(Request $request): array
    {
        $host = $request->getHost();
        $scheme = $request->getScheme();

        return [
            'host' => $host,
            'scheme' => $scheme
        ];
    }
}
