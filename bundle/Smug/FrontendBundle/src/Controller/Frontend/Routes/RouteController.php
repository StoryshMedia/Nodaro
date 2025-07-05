<?php

namespace Smug\FrontendBundle\Controller\Frontend\Routes;

use Symfony\Component\HttpFoundation\Response;
use Smug\Core\Context\Context;
use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class RouteController extends FeBaseController
{
    #[
        Route(
            '/{slug}',
            name: 'frontend',
            defaults: ['slug' => ''],
            requirements: ["slug" => ".+"],
            condition: "service('frontend_route_checker').check(request)"
        )
    ]
    public function index(Request $request, Context $context)
    {
        $this->context->setMode('fe');

        $siteContent = $this->cache->get('smug_frontend_sites_' . DataHandler::getReplaceString('/', '_', $request->getRequestUri()), function (ItemInterface $item) use ($request) {
            $item->expiresAfter(86400); // 1 Stunde

            return $this->getSiteContent($request);
        });

        if (DataHandler::isEmpty($siteContent)) {
            return $this->redirect('/', 301);
        }

        if (DataHandler::isEmpty($siteContent['template'])) {
            $siteContent['template'] = '@SmugFrontend/frontend/index/index.html.twig';
        }

        $siteContent['user'] = ($context->getUser()) ? $this->serializer->serialize($context->getUser()) : null;

        $response = $this->render($siteContent['template'], $siteContent);

        return $this->setHeaders($response, $siteContent);
    }

    private function setHeaders(Response $response, array $siteContent): Response
    {
        $robots = '';
        $noIndex = $siteContent['seo']['noIndex'] ?? true;
        $noFollow = $siteContent['seo']['noFollow'] ?? true;

        $robots = (!$noIndex) ? 'index' : 'noindex';
        $robots .= ', ' . (!$noFollow) ? 'follow' : 'nofollor';


        $response->headers->set('X-Robots-Tag', $robots);

        return $response;
    }
}