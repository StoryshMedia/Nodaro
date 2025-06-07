<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;

trait DispatchDataTrait
{
    public function dispatchData(BaseModel|array $data, Context $context, string $event, string $class, string $eventName): BaseModel|array
    {
        $event = new $event($data, $context, $class);

        $this->dispatch(
            $event,
            $eventName
        );

        return $event->getData();
    }
}