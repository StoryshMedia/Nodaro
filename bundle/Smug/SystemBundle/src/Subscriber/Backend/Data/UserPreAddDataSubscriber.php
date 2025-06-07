<?php

namespace Smug\SystemBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataPreStepEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPreAddDataSubscriber implements EventSubscriberInterface
{
    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;    
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_PRE_CREATED => 'onDataPreCreated'
        ];
    }

    public function onDataPreCreated(DataPreStepEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(User::class)) {
            /** @var User $user */
            $user = $event->getData();

            $user->__set('password', $this->hasher->hashPassword($user, $user->__get('password')));
            $user->__set('emailCanonical', DataHandler::getLowerString($user->__get('email')));
            $user->__set('usernameCanonical', DataHandler::getLowerString($user->__get('username')));
            $event->setData($user);
        }
    }
}