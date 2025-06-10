<?php

namespace Smug\FrontendBundle\Controller\Frontend\Api\Data;

use Smug\AdministrationBundle\Trait\ClassDataProviderTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Service\ListBaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ListController extends BaseController
{
    use ClassDataProviderTrait;

    #[Route(
        '/fe/api/{namespace}/{bundle}/{model}/{field}/{slug}',
        name: 'fe_data_get_sub_data',
        condition: "params['namespace'] !== 'custom' && params['slug'] !== 'assign'",
        methods: ['GET']
    )]
    public function getSubDataAction(
        string $namespace,
        string $bundle,
        string $model,
        string $field,
        string $slug,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $class = $this->getClass($namespace, $bundle, $model);
        $this->context->buildFromRequest(
            $request,
            $class
        );

        $this->context->setConfigItem('field', DataHandler::getCamelCaseString($field));
        $this->context->setIdentifier($slug);
        $this->context->setConfigItem(
            'nested',
            DataHandler::doesKeyExists('nested', $request->query->all()) && DataHandler::getBooleanValue($request->query->all()['nested']) === true
        );
        $this->context->setIdentifierKey('slug');

        return $this->prepareReturn($service->getSubData($this->context));
    }

    #[Route(
        '/fe/api/dynamic/data',
        name: 'fe_data_get_dynamic_data',
        methods: ['POST']
    )]
    public function getDynamicDataAction(
        Request $request
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request
        );

        $this->context->addRepository('main', EntityGenerator::getGeneratedEntity($this->context->getRequestData()['section']));
        $data = $this->context->getEntityByIdentifier($this->context->getRequestData()['item']);

        if (DataHandler::isEmpty($data)) {
            return $this->prepareReturn([
                'success' => false
            ]); 
        }

        return $this->prepareReturn($data->toArray());

    }
}
