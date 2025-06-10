<?php

namespace Smug\SearchBundle\Controller\Frontend\Api\Search;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Smug\SearchBundle\Event\Data\GlobalSearchEvent;
use Smug\SearchBundle\Event\Data\PerformSearchEvent;
use Smug\SearchBundle\Event\Data\SearchStartEvent;
use Smug\SearchBundle\Event\SearchEvents;
use Smug\SearchBundle\Factory\SearchFactory;

class ListController extends BaseController
{
    #[Route(
        '/fe/api/list/search',
        name: 'frontend_list_search',
        methods: ['POST'],
    )]
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
        $result = SearchFactory::getEmptyResult();

        $result = $this->dispatchData(
            $result,
            $this->context,
            PerformSearchEvent::class, '', SearchEvents::PERFORM_SEARCH
        );

        return $this->prepareReturn($result, $request);
    }

    #[Route('/fe/api/search', name: 'fe_search', methods:"POST")]
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