<?php

namespace Smug\SystemBundle\Subscriber\Backend\List;

use ReflectionClass;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataModelLoadedEvent;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGroupDataLoadedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_MODEL_LOADED => 'onModelLoaded'
        ];
    }

    public function onModelLoaded(DataModelLoadedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(UserGroup::class)) {
            $data = $event->getData();
            
            foreach ($data['permission'] as $permissionKey => $permission) {
                $fields = [];
                $rc = new ReflectionClass($permission['modelClass']);

                foreach ($rc->getProperties() as $property) {
                    $fields[] = $property->getName();
                }

                $data['permission'][$permissionKey]['fields'] = $fields;
            }

            $event->setData($data);
        }
    }
}