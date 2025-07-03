<?php

namespace Smug\SystemBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdateDataSubscriber implements EventSubscriberInterface
{
    public function __construct(protected UserPasswordHasherInterface $hasher) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(User::class)) {
            /** @var User $user */
            $user = $event->getContext()->getUser();

            $user->__set('password', $this->hasher->hashPassword($user, $user->__get('password')));

            $event->getContext()->getEntityManager()->persist($user);
            $event->getContext()->getEntityManager()->flush();
        }
    }
}