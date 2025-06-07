<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Trait\ClassDataProviderTrait;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\AdministrationBundle\Trait\SecurityRightTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataConstantsLoadedEvent;
use Smug\Core\Exception\Base\NotAllowedException;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Service\AddBaseService;
use Smug\Core\Service\Base\Service\UpdateBaseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Smug\Core\Events\Backend\Data\DataCreatedEvent;
use Smug\Core\Events\Backend\Data\DataDeletedEvent;
use Smug\Core\Events\Backend\Data\DataPreCreatedEvent;
use Smug\Core\Events\Backend\Data\DataPreUpdatedEvent;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DataController extends BaseController
{
    use ClassDataProviderTrait;
    use SecurityRightTrait;
    use DispatchDataTrait;

    #[Route('/be/api/{namespace}/{bundle}/{model}/add', name: 'add_data', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
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

        try {
            $this->context->isAllowed('wirte', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $this->dispatchData(
            $this->context->getRequestData(),
            $this->context,
            DataPreCreatedEvent::class,
            $entityClass,
            SystemEvents::DATA_BEGIN_CREATE
        );

        $data = $service->add($this->context);

        if ($data['success'] === true) {
            $data['data'] = $this->dispatchData(
                $data['data'],
                $this->context,
                DataCreatedEvent::class,
                $entityClass,
                SystemEvents::DATA_CREATED
            );
        } else {
            return $this->prepareReturn($data, $request);
        }

        if (!DataHandler::isArray($data['data'])) {
            $data['data'] = $data['data']->toArray();
        }
        
        return $this->prepareReturn($data, $request);
    }

    #[Route('/be/api/{namespace}/{bundle}/{model}/save', name: 'save_data', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
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
        $entityClass = EntityGenerator::getGeneratedEntity($domain);

        $constantMappings = $this->dispatchData(
            $constantMappings,
            $this->context,
            DataConstantsLoadedEvent::class,
            $constants,
            SystemEvents::CONSTANTS_LOADED
        );

        $this->context->buildFromRequest(
            $request,
            $domain,
            $constantMappings
        );

        try {
            $this->context->isAllowed('wirte', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }
        
        $this->dispatchData(
            $this->context->getRequestData(),
            $this->context,
            DataPreUpdatedEvent::class,
            $entityClass,
            SystemEvents::DATA_PRE_UPDATE
        );
        
        $data = $service->save($this->context);

        if ($data['success'] === true) {
            $data['data'] = $this->dispatchData($data['data'], $this->context, DataUpdatedEvent::class, $entityClass, SystemEvents::DATA_UPDATED);
        }

        return $this->prepareReturn(['success' => $data['success']], $request);
    }

    #[Route('/be/api/{namespace}/{bundle}/{model}/delete', name: 'delete_data', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
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

        try {
            $this->context->isAllowed('wirte', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $data = $service->delete($this->context);

        if ($data['success'] === true) {
            $data['data'] = $this->dispatchData(
                $this->context->getRequestData(),
                $this->context,
                DataDeletedEvent::class,
                EntityGenerator::getGeneratedEntity($domain),
                SystemEvents::DATA_DELETED
            );
        }

        return $this->prepareReturn($data);
    }

    #[Route('/be/api/{namespace}/{bundle}/{model}/{field}/assign', name: 'assign_data', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function assignAction(
        string $namespace,
        string $bundle,
        string $model,
        string $field,
        Request $request,
        UpdateBaseService $service
    ): JsonResponse {
        $domain = $this->getClass($namespace, $bundle, $model);

        $this->context->buildFromRequest(
            $request,
            EntityGenerator::getGeneratedEntity($domain)
        );

        try {
            $this->context->isAllowed('wirte', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $this->context->setConfigItem('field', $field);

        $data = $service->assign($this->context);

        if ($data['success'] === true) {
            $data['data'] = $this->dispatchData(
                $this->context->getRequestData(),
                $this->context,
                DataDeletedEvent::class,
                $domain,
                SystemEvents::DATA_DELETED
            );
        }

        return $this->prepareReturn($data);
    }
}
