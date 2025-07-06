<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Events\Backend\Data\DataUpdatedEvent;
use Smug\Core\Service\Base\Components\Processor\RemoveProcessor;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Media\MediaSeoAssociation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainUpdateDataSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_UPDATED => 'onDataUpdated'
        ];
    }

    public function onDataUpdated(DataUpdatedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Domain::class)) {
            $requestData = $event->getContext()->getRequestData();
            $associationClass = EntityGenerator::getGeneratedEntity(MediaSeoAssociation::class);
            $event->getContext()->addRepository('media', EntityGenerator::getGeneratedEntity(Media::class));
            $event->getContext()->addRepository('mediaAssociation', $associationClass);

            $seo = $event->getData()->__get('seo');

            foreach ($requestData['seo.images'] ?? [] as $seoImage) {
                $media = $event->getContext()->getEntityByIdentifier(
                        $seoImage['media']['id'],
                        'id',
                        'media'
                );
                $existingMedia = $event->getContext()->getEntitiesByIdentifier($media->__get('id'), 'media', 'mediaAssociation');

                /** @var BaseModel $medium */
                foreach ($existingMedia as $medium) {
                    $event->getContext()->getEntityManager()->remove($medium);
                    $event->getContext()->getEntityManager()->flush();
                }

                $newImage = new $associationClass();
                $newImage->__set('media', $media);
                $newImage->__set('seo', $seo);

                $event->getContext()->getEntityManager()->persist($newImage);
                $event->getContext()->getEntityManager()->flush();
                
                $seo->__add(
                    'images',
                    $newImage
                );
            }

            $seo->__set('title', $requestData['seo']['title']);
            $event->getContext()->getEntityManager()->persist($seo);
            $event->getContext()->getEntityManager()->flush();
        }
    }
}