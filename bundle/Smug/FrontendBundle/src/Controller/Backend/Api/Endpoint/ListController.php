<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Endpoint;

use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Smug\FrontendBundle\Event\Data\ApiEndpointsLoadedEvent;
use Smug\FrontendBundle\Event\FrontendEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListController extends FeBaseController
{
    #[Route('/be/api/custom/api/endpoint/list', name: 'be_api_endpoint_list', methods:"GET")]
    public function selectCoreAction(
    ): JsonResponse {
        $data = [];

        $data = $this->dispatchData(
            $data,
            $this->context,
            ApiEndpointsLoadedEvent::class, '', FrontendEvents::FRONTEND_API_ENDPOINTS_LOADED
        );

        return $this->prepareReturn($data);
    }
}