<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Attribute;

use Attribute;

#[Attribute]
class DefaultValue {
    public array $defaultValue;
    
    public function __construct(string $defaultValue) {
        $this->defaultValue = $defaultValue;
    }
}