<?php

namespace Smug\SystemBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Events\Backend\Data\DataCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SystemBundle\Entity\Permission\Permission;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGroupAddDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_CREATED => 'onDataCreated'
        ];
    }

    public function onDataCreated(DataCreatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(UserGroup::class)) {
            $data = $event->getData();

            $metas = $event->getContext()->getEntityManager()->getMetadataFactory()->getAllMetadata();

            foreach ($metas as $meta) {
                $class = $meta->getName();
                if ($class === BaseModel::class) {
                    continue;
                }
                $permission = new Permission();
                $modelArray = DataHandler::explodeArray('\\', $class);

                $model = DataHandler::getLastArrayElement($modelArray);

                $permission->__set('userGroup', $data);
                $permission->__set('modelClass', $class);
                $permission->__set('model', $model);
                $permission->__set('canRead', false);
                $permission->__set('canWrite', false);
                $permission->__set('disallowedFields', '');
                $permission->__set('hiddenFields', '');
                $permission->__set(
                    'type',
                    DataHandler::getReplaceString('Bundle', '', $modelArray[1])
                );

                $event->getContext()->getEntityManager()->persist($permission);
                $event->getContext()->getEntityManager()->flush();
            }
        }
    }
}