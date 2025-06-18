<?php

namespace Smug\Core\Service\Base\Components\Encryption;

use Smug\Core\Exception\Base\NotAllowedException;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class EncryptionFactory
{
	public static function getEcryptedValue(string $value, bool $check = true): string
    {
        if ($check === true && self::isAlreadyEncrypted($value)) {
            return $value;
        }

        return base64_encode(
            sodium_crypto_secretbox(
                $value,
                self::getNonce(),
                self::getEncryptionKey()
            )
        );
    }

    public static function getDecryptedValue(string $value): string
    {
        return sodium_crypto_secretbox_open(
            base64_decode($value),
            self::getNonce(),
            self::getEncryptionKey()
        );
    }

    protected static function isAlreadyEncrypted($value): bool
    {
        $checkValue = self::getDecryptedValue($value);
        $encryptedCheck = self::getEcryptedValue($checkValue, false);

        return ($value === $encryptedCheck);
    }

    protected static function getEncryptionKey(): string
    {
        if (DataHandler::isEmpty($_ENV['TRUSTED_HOSTS'])) {
            return sodium_hex2bin($_ENV['APP_ENCRYPTION_KEY']);
        }

        $trustedHosts = DataHandler::explodeArray(',', $_ENV['TRUSTED_HOSTS']);

        if (DataHandler::doesKeyExists('SERVER_NAME', $_ENV) && !DataHandler::isInArray($_ENV['SERVER_NAME'], $trustedHosts)) {
            throw new NotAllowedException('Value will not be encrypted from this destination');
        }

        return sodium_hex2bin($_ENV['APP_ENCRYPTION_KEY']);
    }

    protected static function getNonce(): string
    {
        return str_repeat("\0", SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    }
}
