<?php

namespace Smug\Core\Controller\Backend\Api\System\Setting;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Service\Base\Components\Handler\RequestHandler;
use Smug\Core\Service\System\Setting\Listing\ListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingController extends BaseController
{
    #[Route('/be/api/template/get', name: 'get_template_config')]
    #[IsGranted("ROLE_ADMIN")]
    public function getTemplateAction(
        Request $request,
        ListService $service
    ): JsonResponse {
        $data = RequestHandler::getRequestData($request);

	    return $this->prepareReturn($service->getTemplate($data));
    }
}
