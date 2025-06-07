<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Navigation;

use Smug\AdministrationBundle\Service\Components\Factories\NavigationBuilder;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListController extends BaseController
{
    #[Route('/be/api/navigation', name: 'administration_get_navigation')]
    #[IsGranted("ROLE_ADMIN")]
    public function getNavigationAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->context->getUser();

	    return $this->prepareReturn(NavigationBuilder::collect($user->getUserGroup()));
    }
}
