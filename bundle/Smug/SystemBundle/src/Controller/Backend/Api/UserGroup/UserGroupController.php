<?php

namespace Smug\SystemBundle\Controller\Backend\Api\UserGroup;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\ListBaseService;
use Smug\SystemBundle\Entity\Permission\Permission;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;

class UserGroupController extends BaseController
{
    #[Route('/be/api/custom/permission/renew', name: 'renew_permission_models', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getUserProfileAction(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            UserGroup::class
        );
        $this->context->addRepository('permission', Permission::class);

        $data = $this->context->getMainEntity();
        
        $metas = $this->context->getEntityManager()->getMetadataFactory()->getAllMetadata();

        foreach ($metas as $meta) {
            $class = $meta->getName();
            if (
                $class === BaseModel::class ||
                DataHandler::isStringInString($meta->getName(), 'mug\Core\Entity\Generated')
            ) {
                continue;
            }

            if (!DataHandler::isEmpty(
                $this->context->getEntityByMultiple(
                    ['modelClass' => $class, 'userGroup' => $data],
                    'permission'
                )
            )) {
                continue;
            }

            $permission = new Permission();
            $modelArray = DataHandler::explodeArray('\\', $class);

            $model = DataHandler::getLastArrayElement($modelArray);

            $permission->__set('userGroup', $data);
            $permission->__set('modelClass', $class);
            $permission->__set('model', $model);
            $permission->__set('canRead', $data->__get('admin'));
            $permission->__set('canWrite', $data->__get('admin'));
            $permission->__set('disallowedFields', '');
            $permission->__set('hiddenFields', '');
            $permission->__set(
                'type',
                DataHandler::getReplaceString('Bundle', '', $modelArray[1])
            );

            $this->context->getEntityManager()->persist($permission);
            $this->context->getEntityManager()->flush();
        }

        return $this->prepareReturn(['success' => true]);
    }
}
