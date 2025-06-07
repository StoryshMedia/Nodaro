<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Attribute;

use Attribute;

#[Attribute]
class ExtensionTarget {
    public string $targetClass;
    
    public function __construct(string $targetClass) {
        $this->targetClass = $targetClass;
    }

    public function getTargetClass() :string {
        return $this->targetClass;
    }
}