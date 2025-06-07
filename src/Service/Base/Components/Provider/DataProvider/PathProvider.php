<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;

class PathProvider
{
    const MEDIA_PATH = 'media';

    const MEDIA_UPLOAD_DIRECTORY = '/../../../../../../public/';

    public static function getHost(string $mode = 'be'): string
    {
        return ($mode === 'be') ? $_ENV['BACKEND_HOST'] : $_ENV['FRONTEND_HOST'];
    }
	
    public static function getFileHost(): string
    {
        return $_ENV['FILE_HOST'];
    }

    public static function getPageHost(): string
    {
        return $_ENV['PAGE_HOST'];
    }

    public static function getPathWithoutLeadingBackSlash(string $path)
    {
        if (substr($path, 0, 1) === '/') {
            return substr($path, 1);
        } else {
            return $path;
        }
    }

    public static function getPathWithEndingBackSlash(string $path): string
    {
        if (substr($path, -1) !== '/') {
            return $path . '/';
        } else {
            return $path;
        }
    }

    public static function getFoldersInPath(string $path): array
    {
        $finder = new Finder();
        $finder->directories()->in($path)->sortByName()->depth(0);

        $tree = [];

        foreach ($finder as $dir) {
            $tree[] = [
                'title' => $dir->getFilename(),
                'path' => $dir->getRealPath()
            ];
        }

        return $tree;
    }

    public static function getSubFolders(string $path): array
    {
        $finder = new Finder();
        $finder->directories()->in($path)->sortByName()->depth(0);
        $tree = [];

        if (!$finder->hasResults()) {
            return $tree;
        }

        foreach ($finder as $dir) {
            $tree[] = [
                'title' => $dir->getFilename(),
                'path' => $dir->getRealPath(),
                'children' => self::getSubFolders($dir->getRealPath())
            ];
        }

        return $tree;
    }

    public static function getMediaUploadPath(): string
    {
        return __DIR__ . self::MEDIA_UPLOAD_DIRECTORY;
    }

    public static function checkImage(string $path): string
    {
        $checkPath = DataHandler::getReplaceString(self::getHost(), '', $path);
        if (!DataHandler::checkFile($_SERVER['DOCUMENT_ROOT'] . $checkPath)) {
            return self::getHost() . '/site/img/fallback.jpg';
        }

        return $path;
    }
}
