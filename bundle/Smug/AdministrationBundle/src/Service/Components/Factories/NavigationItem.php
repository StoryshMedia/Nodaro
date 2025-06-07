<?php

namespace Smug\AdministrationBundle\Service\Components\Factories;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\Navigation\NavigationItemInterface;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class NavigationItem implements NavigationItemInterface
{
    public function __construct()
    {
        $this->children = new ArrayCollection();    
    }

    private string $label;

    private string $key;

    private int $type;

    private int $position;
    
    private string $path;
    
    private string $parent;
    
    private string $icon;
    
    private ArrayCollection $children;

    public function getLabel(): string {
        return $this->label;
    }

    public function setLabel(string $label): void {
        $this->label = $label;
    }

    public function getKey(): string {
        return $this->key;
    }

    public function setKey(string $key): void {
        $this->key = $key;
    }

    public function getType(): int {
        return $this->type;
    }

    public function setType(int $type): void {
        $this->type = $type;
    }

    public function getPosition(): int {
        return $this->position;
    }

    public function setPosition(int $position): void {
        $this->position = $position;
    }

    public function getPath(): string {
        return $this->path;
    }
    
    public function setPath(string $path): void {
        $this->path = $path;
    }

    public function getParent(): string {
        return $this->parent;
    }

    public function setParent(string $parent): void {
        $this->parent = $parent;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

    public function getChildren(): ArrayCollection {
        return $this->children;        
    }

    public function setChild(NavigationItemInterface $item): void {
        if (!$this->children->contains($item)) {
            $this->children->add($item);
        }
    }

    public function fromArray(array $data): NavigationItem {
        $this->setLabel($data['label']);
        $this->setType($data['type']);
        $this->setPosition($data['position']);
        $this->setPath($data['path']);
        $this->setParent($data['parent']);
        $this->setKey($data['key']);
        $this->setIcon($data['icon']);

        return $this;
    }

    public function toArray(): array {
        return [
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'position' => $this->getPosition(),
            'path' => $this->getPath(),
            'parent' => $this->getParent(),
            'icon' => $this->getIcon(),
            'key' => $this->getKey(),
            'children' => ArrayProvider::getObjectsAsArray($this->getChildren())
        ];
    }
}
