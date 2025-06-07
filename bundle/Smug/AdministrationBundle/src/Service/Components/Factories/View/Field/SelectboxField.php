<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Service\Components\Factories\View\Field;
use Smug\AdministrationBundle\Service\Components\Factories\View\Field\Poperty\Item;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class SelectboxField extends Field
{
    public function __construct()
    {
        $this->items = new ArrayCollection();    
    }
    
    private ArrayCollection $items;

    public function setDisabled(bool $disabled = false): void
    {
        $this->addConfigItem('disabled', $disabled);
    }

    public function getDisabled(): array|bool|string|int|null
    {
        return $this->getConfigItem('disabled');
    }

    public function setGetCall(string $getCall = ''): void
    {
        $this->addConfigItem('getCall', $getCall);
    }

    public function getGetCall(): array|bool|string|int|null
    {
        return $this->getConfigItem('getCall');
    }

    public function getItems(): ArrayCollection {
        return $this->items;        
    }

    public function addItem(Item $item): void {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    public function toArray(): array {
        $array = parent::toArray();
        $array['items'] = ArrayProvider::getObjectsAsArray($this->getItems());
        
        return $array;
    }
}
