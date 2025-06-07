<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Field;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Exception;
use Doctrine\DBAL\Types\Type;
use JsonException;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class JsonField extends Type
{
    public const NAME = 'jsonfield';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        try {
            return DataHandler::getJsonDecode($value, true);
        } catch (Exception $e) {
            return [$e->getMessage()];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (DataHandler::isString($value)) {
            return $value;
        }
        
        try {
            return DataHandler::getJsonEncode($value);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
        
        if (DataHandler::isArray($value) || DataHandler::isObject($value)) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
