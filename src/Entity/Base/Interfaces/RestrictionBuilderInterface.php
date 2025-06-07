<?php

namespace Smug\Core\Entity\Base\Interfaces;

/**
 * Interface RestrictionBuilderInterface
 * @package Smug\Core\Entity\Base\Interfaces
 */
interface RestrictionBuilderInterface
{
    public static function build(string $type, string $field, mixed $value): RestrictionInterface;
}
