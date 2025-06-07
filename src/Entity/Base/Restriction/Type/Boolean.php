<?php

namespace Smug\Core\Entity\Base\Restriction\Type;

use Smug\Core\Entity\Base\Interfaces\RestrictionInterface;

class Boolean implements RestrictionInterface
{
    protected string $field;
    protected $value;

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): mixed {
        return $this->value;
    }

    public function create(string $field, mixed $value): RestrictionInterface
    {
        $this->field = $field;
        $this->value = $value;

        return $this;
    }

    public function check($propertyValue): bool
    {
        return $propertyValue === $this->getValue();
    }
}