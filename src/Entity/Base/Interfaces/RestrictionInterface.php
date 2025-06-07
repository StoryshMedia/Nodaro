<?php

namespace Smug\Core\Entity\Base\Interfaces;

/**
 * Interface RestrictionInterface
 * @package Smug\Core\Entity\Base\Interfaces
 */
interface RestrictionInterface
{
    public function create(string $field, mixed $value);
}
