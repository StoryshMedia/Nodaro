<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;

class NumberProvider
{
    const SYSTEM_BUNDLE_DIRECTORY = '/../../../../../';

    public static function getRandomNumber()
    {
        $length = 3;
        $characters = '012345678901234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = DataHandler::getStringLength($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[DataHandler::getRandomPosition(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function generateNumberRangeNumber(string $pattern, string $key, string $fallback)
    {
        $path = __DIR__ . self::SYSTEM_BUNDLE_DIRECTORY;

        $number = 0;
        $finder = new Finder();

        $finder->files()->name($pattern)->in($path);

        foreach ($finder as $file) {
            $mappingFile = DataHandler::getFile($file->getRealPath());

            $content = DataHandler::getJsonDecode($mappingFile, true);
            if (!DataHandler::doesKeyExists($key, $content)) {
                $number = $content[$fallback] + 1;
                $content[$key] = $content[$fallback] + 1;
            } else {
                $number = $content[$key] + 1;
                $content[$key] = $content[$key] + 1;
            }

            DataHandler::writeFile($file->getRealPath(), DataHandler::getJsonEncode($content));
        }

        return $number;
    }
}
