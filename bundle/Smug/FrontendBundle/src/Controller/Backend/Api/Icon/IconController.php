<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Icon;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Smug\FrontendBundle\Event\Data\IconListLoadedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\FrontendBundle\Event\IconEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class IconController extends FeBaseController
{
    #[Route('/be/api/custom/frontend/icon', name: 'be_get_icon_list', methods:"POST")]
    public function getTemplateList(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ''
        );

        $icons = DataHandler::getJsonDecode(
            DataHandler::getFile(__DIR__ . '/../../../../../config/frontend/icon/frontendBundleIcons.json'),
            true
        );

        $data = $this->dispatchData(
            $icons,
            $this->context,
            IconListLoadedEvent::class,
            '',
            IconEvents::FRONTEND_ICON_LIST_LOADED
        );

        return $this->prepareReturn($data);
    }
}