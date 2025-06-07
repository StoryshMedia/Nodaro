<?php

declare(strict_types=1);

namespace Smug\Core\Entity\Base\Traits;

use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait CopyTrait
{
    public function __clone()
    {
        $SKIPPED_PROPERTIES = [
            'id',
            '__cloner__',
            '__isInitialized__',
            'lazyPropertiesNames',
            'lazyPropertiesDefaults',
            '__initializer__'
        ];

        $clone = new $this();
		$rc = new ReflectionClass($this);

        foreach ($rc->getProperties() as $property) {
            if (DataHandler::isInArray($property->getName(), $SKIPPED_PROPERTIES)) {
                continue;
            }

            if (DataHandler::isInstanceOf($this->__get($property->getName()), Collection::class)) {
                $child = $this->__get($property->getName());
                $clone->__set($property->getName(), $child->__clone());
                continue;
            }

            if (DataHandler::isInstanceOf($this->__get($property->getName()), BaseModel::class)) {
                $child = $this->__get($property->getName());
                $clone->__set($property->getName(), $child->__clone());
                continue;
            }

            $clone->__set($property->getName(), $this->__get($property->getName()));
        }
        
        return $clone;
    }
}
