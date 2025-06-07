<?php

namespace Smug\Core\Entity\Base\Restriction;

use Smug\Core\Entity\Base\Interfaces\RestrictionBuilderInterface;
use Smug\Core\Entity\Base\Interfaces\RestrictionInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class RestrictionBuilder implements RestrictionBuilderInterface
{
    public static function build(string $type, string $field, mixed $value): RestrictionInterface
    {
        $typeClass = 'Smug\Core\Entity\Base\Restriction\Type\\' . DataHandler::getFirstCapitalUpper($type);
        /** @var RestrictionInterface $restriction */
        $restriction = new $typeClass();
        $restriction->create($field, $value);

        return $restriction;
    }
}
