<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Service\Components\Factories\View\View;
use Smug\Core\Events\Backend\View\DetailViewCreatedEvent;

trait ViewEventTrait
{
    public function dispatchViewEvent(View $data, string $constantsClass, string $view): View
    {
        if ($view === 'detail') {
            $event = new DetailViewCreatedEvent($data, $constantsClass);
            $this->dispatch(
                $event,
                SystemEvents::DETAIL_VIEW_CREATED
            );

            return $event->getViewData();
        }

        return $data;
    }
}