<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Template;

use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\FrontendBundle\Event\Data\TemplateListLoadedEvent;
use Smug\FrontendBundle\Event\TemplateEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class TemplateController extends FeBaseController
{
    #[Route('/be/api/custom/template/list', name: 'be_get_template_list', methods:"GET")]
    public function getTemplateList(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ''
        );

        $data = $this->dispatchData(
            [
                [
                    'title' => 'STANDARD',
                    'value' => '@SmugFrontend/frontend/index/index.html.twig'
                ]
            ],
            $this->context,
            TemplateListLoadedEvent::class,
            '',
            TemplateEvents::FRONTEND_TEMPLATE_LIST_LOADED
        );

        return $this->prepareReturn($data);
    }
}