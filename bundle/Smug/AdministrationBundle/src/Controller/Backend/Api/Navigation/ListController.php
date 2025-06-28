<?php

namespace Smug\AdministrationBundle\Controller\Backend\Api\Navigation;

use Smug\AdministrationBundle\Service\Components\Factories\NavigationBuilder;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\ItemInterface;

class ListController extends BaseController
{
    #[Route('/be/api/navigation', name: 'administration_get_navigation')]
    #[IsGranted("ROLE_ADMIN")]
    public function getNavigationAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->context->getUser();
        $cacheKey = 'smug_admin_navigation_' . $user->__get('id');

        $data = $this->cache->get($cacheKey, function (ItemInterface $item) use ($user) {
            $item->expiresAfter(86400); // 1 Stunde

            return NavigationBuilder::collect($user->__get('userGroup'));
        });

	    return $this->prepareReturn($data);
    }
}
