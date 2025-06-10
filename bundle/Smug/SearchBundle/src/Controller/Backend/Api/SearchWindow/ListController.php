<?php

namespace Smug\SearchBundle\Controller\Backend\Api\SearchWindow;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Exception\Base\NotAllowedException;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListController extends BaseController
{
    #[Route('/be/api/custom/domain/search/window/data/{id}', name: 'be_get_domain_search_window_data', methods:"GET")]
    #[IsGranted("ROLE_ADMIN")]
    public function getDomainsearchWindowData(
        $id,
        Request $request
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            EntityGenerator::getGeneratedEntity(Domain::class)
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper('domain'));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $data = $this->context->getEntityByIdentifier($id);
    
        if (DataHandler::isEmpty($data)) {
            return $this->prepareReturn([
                'success' => false,
                'message' => 'DOMAIN_DATA_NOT_FOUND'
            ]);
        }

        if (DataHandler::isEmpty($data->__get('searchWindow'))) {
            $class = EntityGenerator::getGeneratedEntity(SearchWindow::class);
            $searchWindow = new $class();

            $searchWindow->__set('domain', $data);
            $searchWindow->__set('detailPages', DataHandler::getJsonEncode([]));

            $this->context->getEntityManager()->persist($searchWindow);
            $this->context->getEntityManager()->flush();

            $data->__set('searchWindow', $searchWindow);

            $this->context->getEntityManager()->persist($data);
            $this->context->getEntityManager()->flush();

            return $this->prepareReturn([
                'success' => true,
                'data' => $searchWindow->toArray()
            ]);
        }

        return $this->prepareReturn([
            'success' => true,
            'data' => $data->__get('searchWindow')->toArray()
        ]);
    }
}