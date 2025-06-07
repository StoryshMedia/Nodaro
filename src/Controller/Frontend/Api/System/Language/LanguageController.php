<?php

namespace Smug\Core\Controller\Frontend\Api\System\Language;

use Smug\Core\Context\Context;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\SystemBundle\Entity\Language\Language;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LanguageController extends BaseController
{
    #[Route('/fe/api/core/language', name: 'fe_get_languages')]
    public function getLanguagesAction(
        Context $context
    ): JsonResponse {
        $context->addRepository('main', Language::class);

	    return $this->prepareReturn(
            ArrayProvider::getObjectsAsArray($context->getAllEntities())
        );
    }
}
