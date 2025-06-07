<?php

namespace Smug\Core\Entity\Connection\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

class DateType extends \Doctrine\DBAL\Types\DateType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || (is_string($value) && $value !== '')) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getDateFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', 'DateTime']);
    }
}
