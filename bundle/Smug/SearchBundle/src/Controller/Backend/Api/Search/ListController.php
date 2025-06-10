<?php

namespace Smug\SearchBundle\Controller\Backend\Api\Search;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SearchBundle\Event\Data\BackendSearchStartEvent;
use Smug\SearchBundle\Event\Data\SearchCoreLoadingEvent;
use Smug\SearchBundle\Event\SearchEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListController extends BaseController
{
    #[Route('/be/api/custom/core/list', name: 'be_search_core_list', methods:"GET")]
    public function selectCoreAction(
    ): JsonResponse {
        $data = [];

        $data = $this->dispatchData(
            $data,
            $this->context,
            SearchCoreLoadingEvent::class, '', SearchEvents::GET_SEARCH_CORE_LIST
        );

        return $this->prepareReturn($data);
    }

    #[Route('/be/api/custom/search/{area}', name: 'be_simple_search', methods:"POST")]
    public function simpleSearchAction(
        string $area,
        Request $request
    ): JsonResponse {
        $this->context->buildFromRequest(
            $request,
            ''
        );
        $this->context->setRequestData(DataHandler::mergeArray($this->context->getRequestData(), ['area' => $area]));
        $data = [];

        $data = $this->dispatchData(
            $data,
            $this->context,
            BackendSearchStartEvent::class, '', SearchEvents::BACKEND_SEARCH_START
        );

        return $this->prepareReturn($data);
    }
}