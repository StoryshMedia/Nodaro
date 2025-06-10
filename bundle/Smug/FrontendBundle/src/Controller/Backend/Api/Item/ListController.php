<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Item;

use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Smug\FrontendBundle\Event\Data\ItemSelectionSectionLoadedEvent;
use Smug\FrontendBundle\Event\Data\ItemTeaserTemplateLoadedEvent;
use Smug\FrontendBundle\Event\FrontendEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListController extends FeBaseController
{
    #[Route('/be/api/custom/item/teaser/template', name: 'be_api_item_teaser_template', methods:"POST")]
    public function itemTeaserTemplateAction(
    ): JsonResponse {
        $data = [
            [
                'title' => 'STANDARD',
                'value' => '@SmugFrontend/frontend/modules/ItemTeaser/partials/item.html.twig'
            ]
        ];

        $data = $this->dispatchData(
            $data,
            $this->context,
            ItemTeaserTemplateLoadedEvent::class, '',
            FrontendEvents::FRONTEND_ITEM_TEASER_TEMPLATE_LOADED
        );

        return $this->prepareReturn($data);
    }

    #[Route('/be/api/custom/item/select/section', name: 'be_api_item_select_section_list', methods:"GET")]
    public function itemSelectSectionAction(
    ): JsonResponse {
        $data = [];

        $data = $this->dispatchData(
            $data,
            $this->context,
            ItemSelectionSectionLoadedEvent::class, '',
            FrontendEvents::FRONTEND_ITEM_SELECTION_SECTION_LOADED
        );

        return $this->prepareReturn($data);
    }

    #[Route('/be/api/custom/item/select/items', name: 'be_api_item_select_section_items_list', methods:"POST")]
    public function getSectionItemsAction(
        Request $request
    ): JsonResponse {
        // zu post umwandeln, um Klasse zu übergeben. Vorher in Subscribte als Select Value Klasse mitgeben anstatt Tabellennamen
        $this->context->buildFromRequest(
            $request
        );

        $queryBuilder = $this->context->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('items')
            ->from($this->context->getRequestData()['table'], 'items');

        return $this->prepareReturn(ArrayProvider::getObjectsAsArray($queryBuilder->getQuery()->getResult()));
    }
}