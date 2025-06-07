<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View;

use Smug\AdministrationBundle\Interface\View\Items\FieldInterface;

class Field implements FieldInterface
{
    protected string $type;

    protected string $placeholder;

    protected string $identifier;

    protected array $config = [];
    

    public function getType(): string
    {
        return $this->type;
    }
    
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }
    
    public function setPlaceholder(string $placeholder = ''): void
    {
        $this->placeholder = $placeholder;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    
    public function setIdentifier(string $identifier = ''): void
    {
        $this->identifier = $identifier;
    }

    public function getConfig(): array
    {
        return $this->config;   
    }
    
    public function setConfig(array $config = []): void
    {
        $this->config = $config;
    }
    
    public function addConfigItem(string $key, array|bool|string|int $value): void
    {
        $this->config[$key] = $value;
    }

    public function getConfigItem(string $key): array|bool|string|int|null
    {
        return $this->config[$key] ?? null;
    }

    public function fromArray(array $data): Field {
        $this->setType($data['type']);
        $this->setPlaceholder($data['placeholder'] ?? '');
        $this->setIdentifier($data['identifier']);
        $this->setConfig($data['config']);

        return $this;
    }

    public function toArray(): array {
        return [
            'type' => $this->getType(),
            'placeholder' => $this->getPlaceholder(),
            'identifier' => $this->getIdentifier(),
            'config' => $this->getConfig()
        ];
    }
}
