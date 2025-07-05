<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Security;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends BaseController
{
    #[Route('/be_logout', name: 'be_logout')]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
