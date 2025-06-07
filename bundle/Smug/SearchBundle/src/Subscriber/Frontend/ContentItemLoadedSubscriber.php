<?php

namespace Smug\SearchBundle\Subscriber\Frontend;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Frontend\Site\ContentItemLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Smug\FrontendBundle\Subscriber\Frontend\ContentItemRenderingSubscriber;

class ContentItemLoadedSubscriber extends ContentItemRenderingSubscriber
{
    protected array $identifiers;

    protected Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->identifiers = [
            'SmugSimpleNavigation'
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FrontendEvents::FRONTEND_CONTENT_ITEM_LOADED => 'onContentItemLoaded'
        ];
    }

    public function onContentItemLoaded(ContentItemLoadedEvent $event): void
    {
        $data = $event->getData();

        if (self::doProcess($data, $this->identifiers[0])) {
            $site = $this->context->getEntityManager()->getRepository(EntityGenerator::getGeneratedEntity(Site::class))->findOneBy(['id' => $data['site']['id'] ?? '']);

            if (!DataHandler::isEmpty($site->__get('domain')->__get('searchWindow'))) {
                $data['variables']['searchWindowData'] = $site->__get('domain')->__get('searchWindow')->toArray();
            }
        }

        $event->setData($data);
    }
}