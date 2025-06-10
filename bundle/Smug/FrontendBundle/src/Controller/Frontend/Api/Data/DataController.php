<?php

namespace Smug\FrontendBundle\Controller\Frontend\Api\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Trait\ClassDataProviderTrait;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataConstantsLoadedEvent;
use Smug\Core\Events\Backend\Data\DataCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\AddBaseService;
use Smug\Core\Service\Base\Service\UpdateBaseService;
use Smug\FrontendBundle\Service\Search\SearchService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends BaseController
{
    use ClassDataProviderTrait;
    use DispatchDataTrait;

    #[
        Route(
            '/fe/api/{namespace}/{bundle}/{model}/delete',
            name: 'fe_delete_data',
            methods: ['PUT'],
            condition: "params['namespace'] !== 'custom'"
        )
    ]
    public function deleteAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        UpdateBaseService $service
    ): JsonResponse {
        $domain = $this->getClass($namespace, $bundle, $model);

        $this->context->buildFromRequest(
            $request,
            $domain
        );

        $data = $service->delete($this->context);

        return $this->prepareReturn($data);
    }

    #[
        Route(
            '/fe/api/{namespace}/{bundle}/{model}/add',
            name: 'add_fe_data',
            methods: ['POST'],
            condition: "params['namespace'] !== 'custom'"
        )
    ]
    public function addAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        AddBaseService $service
    ): JsonResponse {
        $constants = $this->getConstants($namespace, $bundle, $model);
        $constantMappings = DataHandler::doesClassExist($constants) ? $constants::MAPPING : [];

        $domain = $this->getClass($namespace, $bundle, $model);
        $entityClass = EntityGenerator::getGeneratedEntity($domain);

        $constantMappings = $this->dispatchData(
            $constantMappings,
            $this->context,
            DataConstantsLoadedEvent::class,
            $entityClass,
            SystemEvents::CONSTANTS_LOADED
        );

        $this->context->buildFromRequest(
            $request,
            $domain,
            $constantMappings
        );

        $this->context->setMode('fe');

        $data = $service->add($this->context);

        if ($data['success'] === true) {
            $data['data'] = $this->dispatchData(
                $data['data'],
                $this->context,
                DataCreatedEvent::class,
                $entityClass,
                SystemEvents::DATA_CREATED
            );
        }

        if (!DataHandler::isArray($data['data'])) {
            $data['data'] = $data['data']->toArray();
        }
        
        return $this->prepareReturn($data, $request);
    }

    #[
        Route(
            '/fe/api/{namespace}/{bundle}/{model}/save',
            name: 'save_fe_data',
            methods: ['PUT'],
            condition: "params['namespace'] !== 'custom'"
        )
    ]
    public function saveAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        UpdateBaseService $service
    ): JsonResponse {
        $constants = $this->getConstants($namespace, $bundle, $model);
        $constantMappings = DataHandler::doesClassExist($constants) ? $constants::MAPPING : [];

        $domain = $this->getClass($namespace, $bundle, $model);

        $this->context->buildFromRequest(
            $request,
            $domain,
            $constantMappings
        );
        $this->context->setMode('fe');

        $data = $service->save($this->context);

        return $this->prepareReturn(['success' => $data['success']], $request);
    }

    #[Route('/fe/api/{namespace}/{bundle}/{model}/search', name: 'search_fe_data', methods: ['POST'])]
    public function searchAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        SearchService $service
    ): JsonResponse {
        $domain = $this->getClass($namespace, $bundle, $model);

        $this->context->buildFromRequest(
            $request,
            $domain
        );

        $data = $service->getSearchResults($this->context);

        return $this->prepareReturn($data, $request);
    }
}
