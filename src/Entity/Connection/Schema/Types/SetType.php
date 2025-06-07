<?php

namespace Smug\Core\Entity\Connection\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class SetType extends Type
{
    public const TYPE = 'set';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $quotedValues = array_map([$platform, 'quoteStringLiteral'], $fieldDeclaration['unquotedValues']);

        return sprintf('SET(%s)', implode(', ', $quotedValues));
    }

    public function getName(): string
    {
        return static::TYPE;
    }
}
