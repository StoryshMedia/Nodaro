<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\View\Items\FieldInterface;
use Smug\AdministrationBundle\Interface\View\Items\RowInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class Row implements RowInterface
{
    public function __construct()
    {
        $this->fields = new ArrayCollection();    
    }

    private string $class;
    
    private ArrayCollection $fields;

    public function getClass(): string {
        return $this->class;
    }

    public function setClass(string $class): void {
        $this->class = $class;
    }

    public function getFields(): ArrayCollection {
        return $this->fields;        
    }

    public function addField(FieldInterface $field): void {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
        }
    }

    public function fromArray(array $data): Row {
        $this->setClass($data['class'] ?? '');

        if (DataHandler::doesKeyExists('fields', $data)) {
            foreach ($data['fields'] as $field) {
                if (DataHandler::isInstanceOf($field, FieldInterface::class)) {
                    $this->addField($field);
                }
            }
        }

        return $this;
    }

    public function toArray(): array {
        return [
            'fields' => ArrayProvider::getObjectsAsArray($this->getFields()),
            'class' => $this->getClass()
        ];
    }
}
