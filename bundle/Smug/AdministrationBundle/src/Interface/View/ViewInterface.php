<?php

namespace Smug\AdministrationBundle\Interface\View;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\View\Items\TabInterface;
use Smug\AdministrationBundle\Service\Components\Factories\View\View;

interface ViewInterface {
    public function getConfig(): array;

    public function setConfig(array $config): void;

    public function addConfigItem(string $key, array|bool|string|int $item): void;

    public function getTabs(): ArrayCollection;

    public function getTab(string $headline): ?TabInterface;

    public function addTab(TabInterface $tab): void;

    public function fromArray(array $data): View;

    public function toArray(): array;
}