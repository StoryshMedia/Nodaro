<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\User;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\AdministrationBundle\Service\User\Listing\ListService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends BaseController
{
    #[Route('/be/api/user/current/get', name: 'personal_get_current_user')]
    public function getCurrentUserAction(ListService $service): JsonResponse
    {
	    return $this->prepareReturn(['data' => $service->getUser()]);
    }

    #[Route('/be/api/user/account/get', name: 'personal_get_user_account')]
    public function getUserAccountAction(ListService $service): JsonResponse
    {
	    return $this->prepareReturn(['data' => $service->getUser()]);
    }
}
