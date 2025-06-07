<?php

namespace Smug\Core\Entity\Connection\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

class TimeType extends \Doctrine\DBAL\Types\TimeType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || (is_string($value) && $value !== '')) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }
}
