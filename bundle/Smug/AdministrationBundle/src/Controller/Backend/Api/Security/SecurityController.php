<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Security;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends BaseController
{
    #[Route('/be_login', name: 'be_login')]
    public function login(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $session->set('user', $user);

        return $this->json([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/be_logout', name: 'be_logout')]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
