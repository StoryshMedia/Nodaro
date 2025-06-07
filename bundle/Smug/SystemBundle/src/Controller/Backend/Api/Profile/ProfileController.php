<?php

namespace Smug\SystemBundle\Controller\Backend\Api\Profile;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends BaseController
{
    #[Route('/be/api/custom/profile', name: 'get_user_profile', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getUserProfileAction(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            User::class
        );

        $user = [];

        if ($this->context->getUser()) {
            $user = $this->context->getUser()->toArray();
        }

        return $this->prepareReturn($user);
    }

    #[Route('/be/api/custom/screen/unlock', name: 'unlock', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function unlockScreenAction(Request $request, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            User::class
        );

        /** @var User $user */
        $user = $this->context->getUser();

        if (DataHandler::isEmpty($user)) {
            return $this->prepareReturn(['success' => false]); 
        }

        return $this->prepareReturn(['success' => $hasher->isPasswordValid($user, $this->context->getRequestData()['password'])]);
    }
}
