<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\User;

use Smug\SystemBundle\Entity\User\User;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\AdministrationBundle\Service\User\Listing\ListService;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends BaseController
{
    #[Route('/be/api/loggedIn', name: 'administration_logged_in')]
    #[IsGranted("ROLE_ADMIN")]
    public function isLoggedInAction(): JsonResponse
    {
	    if (
            DataHandler::isEmpty($this->context->getUser()) ||
            !DataHandler::isInstanceOf(
                $this->context->getUser(),
                EntityGenerator::getGeneratedEntity(User::class)
            )
       ) {
            return $this->prepareReturn([
                'loggedIn' => false
            ]);
       }
	
	    return $this->prepareReturn([
            'loggedIn' => true
        ]);
    }

    #[Route('/be/api/allowed', name: 'administration_isAllowed')]
    #[IsGranted("ROLE_ADMIN")]
    public function isAllowedAction(
        Request $request
    ): JsonResponse
    {
	    if (
            DataHandler::isEmpty($this->context->getUser()) ||
            !DataHandler::isInstanceOf($this->context->getUser(), EntityGenerator::getGeneratedEntity(User::class))
        ) {
            return $this->prepareReturn([
                'read' => false,
                'write' => false
            ]);
        }

        if ($this->context->getUser()->__get('userGroup')->__get('admin') === true) {
            return $this->prepareReturn([
                'read' => true,
                'write' => true,
                'disallowedFields' => '',
                'hiddenFields' => ''
            ]);
        }

        $data = DataHandler::getJsonDecode($request->getContent(), true);

	    return $this->prepareReturn([
            'read' => $this->context->isAllowed('read', $data['class']),
            'write' => $this->context->isAllowed('write', $data['class']),
            'disallowedFields' => $this->context->getSpecialFields($data['class'], 'disallowedFields'),
            'hiddenFields' => $this->context->getSpecialFields($data['class'], 'hiddenFields')
        ]);
    }

    #[Route('/be/api/user/feeds/get', name: 'personal_get_user_feeds')]
    #[IsGranted("ROLE_ADMIN")]
    public function getUserFeedsAction(ListService $service): JsonResponse
    {
	    $users = $service->getUserFeeds();

	    return $this->prepareReturn($users);
    }
}
