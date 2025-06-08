<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Search;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\AdministrationBundle\Event\Data\BackendSearchStartEvent;
use Smug\AdministrationBundle\Event\Data\SearchCoreLoadingEvent;
use Smug\AdministrationBundle\Event\SearchEvents;
use Smug\AdministrationBundle\Event\Data\GlobalSearchEvent;
use Smug\AdministrationBundle\Event\Data\PerformSearchEvent;
use Smug\AdministrationBundle\Event\Data\SearchStartEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route(
        '/be/api/list/search',
        name: 'belist_search',
        methods: ['POST'],
    )]
    #[IsGranted("ROLE_ADMIN")]
    public function getPaginatedAction(
        Request $request
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ''
        );

        $data = $this->dispatchData(
            $this->context->getRequestData(),
            $this->context,
            SearchStartEvent::class, '', SearchEvents::SEARCH_START
        );

        $this->context->setConfig($data);
        $result = [
            'label' => 'SEARCH_RESULTS',
            'results' => [],
            'marketing' => []
        ];

        $result = $this->dispatchData(
            $result,
            $this->context,
            PerformSearchEvent::class, '', SearchEvents::PERFORM_SEARCH
        );

        return $this->prepareReturn($result, $request);
    }

    #[Route('/be/api/search', name: 'be_search', methods:"POST")]
    #[IsGranted("ROLE_ADMIN")]
    public function searchAction(
        Request $request
    ): JsonResponse {
        $this->context->buildFromRequest(
            $request,
            ''
        );

        $data = $this->dispatchData(
            $this->context->getRequestData(),
            $this->context,
            GlobalSearchEvent::class, '', SearchEvents::GLOBAL_SEARCH_START
        );

        $this->context->setConfig($data);
        $result = SearchFactory::getEmptyResult();

        $result = $this->dispatchData(
            $result,
            $this->context,
            GlobalSearchEvent::class, '', SearchEvents::GLOBAL_PERFORM_SEARCH
        );

        return $this->prepareReturn($result, $request);
    }
}