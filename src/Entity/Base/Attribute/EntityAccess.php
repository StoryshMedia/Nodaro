<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Attribute;

use Attribute;

#[Attribute]
class EntityAccess {
    public array $config;
    
    public function __construct(array $config = []) {
        $this->config = $config;
    }

    public function getConfig() :array {
        return $this->config;
    }
}