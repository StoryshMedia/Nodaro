<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Field;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Exception;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class SlugField extends Type
{
    public const NAME = 'slugField';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if (!DataHandler::isString($value)) {
            try {

            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (DataHandler::isArray($value) || DataHandler::isObject($value)) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }

        return (string) $value;
    }

    public function getBindingType()
    {
        return \PDO::PARAM_STR;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
