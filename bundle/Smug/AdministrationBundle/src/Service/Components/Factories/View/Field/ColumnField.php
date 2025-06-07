<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\View\Items\FieldInterface;
use Smug\AdministrationBundle\Service\Components\Factories\View\Field;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class ColumnField extends Field
{
    public function __construct()
    {
        $this->items = new ArrayCollection();    
    }
    
    private ArrayCollection $items;

    public function getItems(): ArrayCollection {
        return $this->items;        
    }

    public function addItem(FieldInterface $field): void {
        if (!$this->items->contains($field)) {
            $this->items->add($field);
        }
    }
    public function setFieldClasses(string $fieldClasses = ''): void
    {
        $this->addConfigItem('fieldClasses', $fieldClasses);
    }

    public function getFieldClasses(): array|bool|string|int|null
    {
        return $this->getConfigItem('fieldClasses');
    }

    public function toArray(): array {
        $array = parent::toArray();
        $array['items'] = ArrayProvider::getObjectsAsArray($this->getItems());
        
        return $array;
    }
}
