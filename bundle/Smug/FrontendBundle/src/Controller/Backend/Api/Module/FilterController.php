<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Module;

use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\FrontendBundle\Event\Data\FilterLoadedEvent;
use Smug\FrontendBundle\Event\ModuleEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class FilterController extends FeBaseController
{
    #[Route('/be/api/custom/module/filter', name: 'be_module_get_filters', methods:"POST")]
    public function scan(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ContentItemModule::class
        );

        $module = $this->context->getEntityByIdentifier($this->context->getRequestData()['module']['id']);
        $data = [
            'items' => [],
            'module' => $module->__get('module')
        ];

        $data = $this->dispatchData($data, $this->context, FilterLoadedEvent::class, Module::class, ModuleEvents::FRONTEND_MODULE_FILTER_LOAD);

        return $this->prepareReturn($data);
    }
}