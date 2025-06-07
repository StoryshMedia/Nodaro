<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

class UploadErrorMessageProvider
{
    const INVALID_ERROR = 'Dieses Dateiformat wird nicht unterstützt!';

    const FILE_SIZE_ERROR = 'Die Datei ist zu groß und konnte nicht hochgeladen werden!';

    const FILE_TYPE_ERROR = 'Dateifehler!';

    public static function getInvalidError(string $fileName): array {
        return [
            'success' => false,
            'message' => self::INVALID_ERROR,
            'fileName' => $fileName
        ];
    }

    public static function getFileSizeError(string $fileName): array {
        return [
            'success' => false,
            'message' => self::FILE_SIZE_ERROR,
            'fileName' => $fileName
        ];
    }

    public static function getFileTypeError(string $fileName): array {
        return [
            'success' => false,
            'message' => self::FILE_TYPE_ERROR,
            'fileName' => $fileName
        ];
    }
}
