<?php

namespace Smug\Core\Subscriber\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtInvalidTokenSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_INVALID => ['onJwtInvalid']
        ];
    }

    public function onJwtInvalid(JWTInvalidEvent $event): void
    {
        if (
            !str_starts_with($event->getRequest()->getPathInfo(), '/be/api') &&
            !str_starts_with($event->getRequest()->getPathInfo(), '/fe/api')
        ) {
            header('Location: /', true, 302);
            die();
        }
    }
}