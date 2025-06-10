<?php

namespace Smug\FrontendBundle\Subscriber\Backend\Data;

use Doctrine\DBAL\ParameterType;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Backend\Data\DataCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Service\Factory\ContentItemPositionFactory;
use Smug\FrontendBundle\Service\Frontend\Renderer\FrontendModuleRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentItemAddDataSubscriber implements EventSubscriberInterface
{
    protected FrontendModuleRenderer $renderer;

    public function __construct(FrontendModuleRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_CREATED => 'onDataCreated'
        ];
    }

    public function onDataCreated(DataCreatedEvent $event): void
    {
        $data = $event->getData();
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(ContentItem::class)) {
            if (DataHandler::isArray($data)) {
                $event->getContext()->addRepository('contentItem', EntityGenerator::getGeneratedEntity(ContentItem::class));
                $data = $event->getContext()->getEntityByIdentifier($data['id'], 'id', 'contentItem');
            }

            if (!DataHandler::isEmpty($data->__get('site'))) {
                $requestData = $event->getContext()->getRequestData();
                $event->getContext()->addRepository('contentItemModule', EntityGenerator::getGeneratedEntity(ContentItemModule::class));
                $module = $event->getContext()->getEntityByIdentifier($requestData['module']->__get('id'), 'id', 'contentItemModule');

                $queryBuilder = $event->getContext()->getEntityManager()->createQueryBuilder();

                $queryBuilder->select('c')
                    ->from(EntityGenerator::getGeneratedEntity(ContentItem::class), 'c')
                    ->where('c.id NOT LIKE :entityId')
                    ->andWhere('c.position >= :position')
                    ->andWhere('c.site = :site')
                    ->setParameter('entityId', $data->__get('id'), ParameterType::STRING)
                    ->setParameter('position', $data->__get('position'), ParameterType::INTEGER)
                    ->setParameter('site', $data->__get('site')->__get('id'), ParameterType::STRING);
                
                $items = $queryBuilder->getQuery()->getResult();
                foreach ($items as $item) {
                    if ($item->__get('id') !== $data->__get('id')) {
                        $item->__set('position', $item->__get('position') + 1);

                        $event->getContext()->getEntityManager()->persist($item);
                        $event->getContext()->getEntityManager()->flush();
                    }
                }

                $module->__set('content', $data);

                $event->getContext()->getEntityManager()->persist($module);
                $event->getContext()->getEntityManager()->flush();

                $data = $event->getContext()->getEntityByIdentifier($data->getId())->toArray();
            
                ContentItemPositionFactory::renewPositions($event->getContext(), $data, 'site');

                $data['children'] = [];
                $data = $this->renderer->render($data);
                $event->setData($data);
            }
        }
    }
}