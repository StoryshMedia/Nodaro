<?php

namespace Smug\FrontendBundle\Controller\Frontend\Routes;

use Smug\Core\Context\Context;
use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RouteController extends FeBaseController
{
    #[Route('/fe/api/visit/{mode}/list', name: 'fe_set_list_visit', methods:"GET")]
    public function listVisitAction(string $mode): JsonResponse
    {
        return $this->prepareReturn($this->setSiteVisit($mode, '', 'list'));
    }

    #[Route('/fe/api/visit/{mode}/{name}', name: 'fe_set_visit', methods:"GET")]
    public function visitAction(string $mode, string $name): JsonResponse
    {
        return $this->prepareReturn($this->setSiteVisit($mode, $name));
    }
    
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
        $siteContent = $this->getSiteContent($request);

        if (DataHandler::isEmpty($siteContent)) {
            return $this->redirect('/', 301);
        }

        if (DataHandler::isEmpty($siteContent['template'])) {
            $siteContent['template'] = '@SmugFrontend/frontend/index/index.html.twig';
        }

        $siteContent['user'] = ($context->getUser()) ? $context->getUserArray() : null;

        return $this->render($siteContent['template'], $siteContent);
    }
}