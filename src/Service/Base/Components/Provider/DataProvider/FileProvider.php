<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\HttpKernel\KernelInterface;

class FileProvider
{
    /** @var string $rootDir */
    public string $rootDir;

    /** @var string $appDirectory */
    public string $appDirectory;

    /** @var string $bundleDirectory */
    public string $bundleDirectory;

    /** @var string $publicDirectory */
    public string $publicDirectory;

    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
        $this->appDirectory = $this->rootDir . '/custom/';
        $this->bundleDirectory = $this->rootDir . '/src/';
        $this->publicDirectory = $this->rootDir . '/public/';
    }

    public static function checkForImage(string $path): string
    {
        if (DataHandler::getStringLength($path) === 0) {
            return self::getFallbackImage();
        }

        if (DataHandler::isStringInString($path, PathProvider::getHost())) {
            return $path;
        }

        if (!DataHandler::doesFileExist(PathProvider::getMediaUploadPath() . $path)) {
            return self::getFallbackImage();
        }

        if (DataHandler::getSubString($path, 0, 1) !== '/') {
            return PathProvider::getHost() . '/' . $path;
        }

        return PathProvider::getHost() . $path;
    }

    private static function getFallbackImage(): string
    {
        return PathProvider::getHost() . '/Resources/media/image/noimage.jpg';
    }
}
