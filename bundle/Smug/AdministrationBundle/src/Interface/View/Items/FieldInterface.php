<?php

namespace Smug\AdministrationBundle\Interface\View\Items;

interface FieldInterface {
    public function getType(): string;
    
    public function setType(string $type): void;

    public function getPlaceholder(): string;
    
    public function setPlaceholder(string $placeholder = ''): void;

    public function getIdentifier(): string;
    
    public function setIdentifier(string $identifier = ''): void;

    public function getConfig(): array;
    
    public function setConfig(array $config = []): void;
    
    public function addConfigItem(string $key, array|bool|string|int $value): void;
}