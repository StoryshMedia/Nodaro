<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base\Field;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Exception;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ScriptField extends Type
{
    public const NAME = 'scriptField';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($column);
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

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
