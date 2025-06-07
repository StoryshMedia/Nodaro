<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Trait\ClassDataProviderTrait;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\AdministrationBundle\Trait\SecurityRightTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataModelListLoadedEvent;
use Smug\Core\Events\Backend\Data\DataModelLoadedEvent;
use Smug\Core\Exception\Base\NotAllowedException;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Service\ListBaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListController extends BaseController
{
    use ClassDataProviderTrait;
    use SecurityRightTrait;
    use DispatchDataTrait;

    #[Route(
        '/be/api/{namespace}/{bundle}/{model}/paginated',
        name: 'data_get_paginated',
        methods: ['POST'],
        condition: "params['namespace'] !== 'custom'"
    )]
    #[IsGranted("ROLE_ADMIN")]
    public function getPaginatedAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $constants = $this->getConstants($namespace, $bundle, $model);
        $class = $this->getClass($namespace, $bundle, $model);

        $this->context->buildFromRequest(
            $request,
            EntityGenerator::getGeneratedEntity($class),
            $constants::PAGINATION_CONFIG
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        return $this->prepareReturn($service->getPaginated($this->context), $request);
    }

    #[Route(
        '/be/api/{namespace}/{bundle}/{model}/{id}',
        name: 'data_get_single',
        methods: ['GET'],
        requirements: ['namespace' => "\b(?!abc\b)\w+"],
        condition: "params['namespace'] !== 'custom'"
    )]
    #[IsGranted("ROLE_ADMIN")]
    public function getSingleAction(
        string $namespace,
        string $bundle,
        string $model,
        $id,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $class = EntityGenerator::getGeneratedEntity($this->getClass($namespace, $bundle, $model));
        $this->context->buildFromRequest(
            $request,
            $class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $this->context->setIdentifier($id);

        $data = $service->getSingle($this->context);

        $data = $this->dispatchData($data, $this->context, DataModelLoadedEvent::class, $class, SystemEvents::DATA_MODEL_LOADED);

        return $this->prepareReturn($data);
    }

    #[Route(
        '/be/api/{namespace}/{bundle}/{model}',
        name: 'get_data',
        methods: ['GET'],
        condition: "params['namespace'] !== 'custom'"
    )]
    #[IsGranted("ROLE_ADMIN")]
    public function getDataAction(
        string $namespace,
        string $bundle,
        string $model,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $class = EntityGenerator::getGeneratedEntity($this->getClass($namespace, $bundle, $model));
        $this->context->buildFromRequest(
            $request,
            $class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $data = $this->dispatchData(
            $service->getData($this->context),
            $this->context,
            DataModelListLoadedEvent::class,
            $class,
            SystemEvents::DATA_MODEL_LIST_LOADED
        );

        return $this->prepareReturn($data);
    }

    #[Route(
        '/be/api/{namespace}/{bundle}/{model}/{field}/{id}',
        name: 'data_get_sub_data',
        condition: "params['namespace'] !== 'custom' && params['id'] !== 'assign'",
        methods: ['GET']
    )]
    #[IsGranted("ROLE_ADMIN")]
    public function getSubDataAction(
        string $namespace,
        string $bundle,
        string $model,
        string $field,
        $id,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $class = $this->getClass($namespace, $bundle, $model);
        $this->context->buildFromRequest(
            $request,
            $class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper($model));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }
        
        $this->context->setConfigItem('field', DataHandler::getCamelCaseString($field));
        $this->context->setConfigItem(
            'nested',
            DataHandler::doesKeyExists('nested', $request->query->all()) && DataHandler::getBooleanValue($request->query->all()['nested']) === true
        );
        $this->context->setIdentifier($id);

        return $this->prepareReturn($service->getSubData($this->context));
    }
}
