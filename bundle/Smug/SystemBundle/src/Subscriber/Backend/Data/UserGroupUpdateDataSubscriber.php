<?php

namespace Smug\SystemBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SystemBundle\Entity\Permission\Permission;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGroupUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(UserGroup::class)) {
            $permissions = $event->getContext()->getRequestData()['permission'] ?? [];

            $permissionRepository = $event->getContext()->getEntityManager()->getRepository(Permission::class);

            foreach ($permissions as $model) {
                foreach ($model as $permission) {
                    if (DataHandler::isString($permission)) {
                        break;
                    }
                    
                    /** @var Permission $permissionObject */
                    $permissionObject = $permissionRepository->findOneBy(['id' => $permission['id']]);

                    if (DataHandler::isEmpty($permissionObject)) {
                        continue;
                    }

                    $permissionObject->__set('canRead', boolval($permission['canRead']));
                    $permissionObject->__set('canWrite', boolval($permission['canWrite']));
                    $permissionObject->__set('hiddenFields', $permission['hiddenFields'] ?? '');
                    $permissionObject->__set('disallowedFields', $permission['disallowedFields'] ?? '');

                    $event->getContext()->getEntityManager()->persist($permissionObject);
                    $event->getContext()->getEntityManager()->flush();
                }
            }
        }
    }
}