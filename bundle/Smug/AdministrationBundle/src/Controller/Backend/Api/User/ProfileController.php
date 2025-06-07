<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\User;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Service\Base\Components\Handler\RequestHandler;
use Smug\AdministrationBundle\Service\User\Listing\ListService;
use Smug\AdministrationBundle\Service\User\Update\UpdateService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/be/api/user/password/change', name: 'personal_change_user_password')]
    #[IsGranted("ROLE_ADMIN")]
    public function changePasswordAction(
        Request $request,
        UpdateService $service
    ): JsonResponse {
        $data = RequestHandler::getRequestData($request);

        $save = $service->changePassword($data);

        return $this->prepareReturn($save, $request);
    }

    #[Route('/be/api/user/password/reset', name: 'personal_reset_user_password')]
    #[IsGranted("ROLE_ADMIN")]
    public function resetPasswordAction(
        Request $request,
        UpdateService $service
    ): JsonResponse {
        $save = $service->changePassword(['newPassword' => ''], true);

	    return $this->prepareReturn($save, $request);
    }
}
