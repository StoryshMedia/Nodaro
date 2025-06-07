<?php

namespace Smug\AdministrationBundle\Interface\Navigation;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Service\Components\Factories\NavigationItem;

interface NavigationItemInterface {
    public function getLabel(): string;

    public function setLabel(string $label): void;

    public function getType(): int;

    public function setType(int $type): void;

    public function getPath(): string;
    
    public function setPath(string $path): void;

    public function getPosition(): int;
    
    public function setPosition(int $position): void;

    public function getParent(): string;

    public function setParent(string $parent);

    public function getIcon(): string;

    public function setIcon(string $icon);

    public function getChildren(): ArrayCollection;

    public function setChild(NavigationItemInterface $item): void;

    public function fromArray(array $data): NavigationItem;

    public function toArray(): array;
}