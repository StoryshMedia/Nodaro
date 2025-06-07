<?php

namespace Smug\Core\Entity\Connection\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{
    public const TYPE = 'enum';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $quotedValues = array_map([$platform, 'quoteStringLiteral'], $fieldDeclaration['unquotedValues']);

        return sprintf('ENUM(%s)', implode(', ', $quotedValues));
    }

    public function getName(): string
    {
        return static::TYPE;
    }
}
