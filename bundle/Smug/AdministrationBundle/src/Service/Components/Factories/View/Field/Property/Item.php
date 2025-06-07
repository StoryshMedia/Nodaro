<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field\Property;

class Item
{
    protected string $title;

    protected string|array|int|bool $value;
    
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function setValue(string|array|int|bool $value): void
    {
        $this->value = $value;
    }

    public function getValue(): string|array|int|bool
    {
        return $this->value;
    }
}
