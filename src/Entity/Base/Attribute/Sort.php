<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Attribute;

use Attribute;

#[Attribute]
class Sort {
    public array $field;
    
    public function __construct(string $field) {
        $this->field = $field;
    }
}