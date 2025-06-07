<?php

namespace Smug\SearchBundle\Subscriber\Backend\View;

use ReflectionClass;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Service\Components\Factories\ViewBuilder;
use Smug\Core\Events\Backend\View\DetailViewCreatedEvent;
use Smug\FrontendBundle\Constants\Views\Backend\DomainConstants;
use Smug\FrontendUserBundle\Entity\FrontendUser\FrontendUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainDetailViewSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DETAIL_VIEW_CREATED => 'onCreateDetailView'
        ];
    }

    public function onCreateDetailView(DetailViewCreatedEvent $event): void
    {
        $data = $event->getViewData();

        if ($event->getClass() === DomainConstants::class) {
            $rc = new ReflectionClass(FrontendUser::class);
            $data->addTab(
                ViewBuilder::buildTab([
                    'headline' => 'SEARCH_WINDOW',
                    'icon' => 'IconSearch',
                    'rows' => [
                        [
                            'class' => 'grid grid-cols-1 my-5',
                            'fields' => [
                                [
                                    'type' => 'SearchWindowData',
                                    'placeholder' => 'SEARCH_WINDOW_DATA',
                                    'config' => [
                                        'saveCall' => '/be/api/smug/search/searchWindow/save',
                                        'getCall' => '/be/api/custom/domain/search/window/data/',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ], $rc)
            );
        }
        
        $event->setViewData($data);
    }
}