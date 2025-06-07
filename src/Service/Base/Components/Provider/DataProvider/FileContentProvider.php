<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;
use \Exception;

class FileContentProvider
{
    const SYSTEM_BUNDLE_DIRECTORY = '/../../../../../';

    const BUNDLE_DIRECTORY = '/../../../../../../bundle/';

    const BASE_DIRECTORY = '/../../../../../../';

    const SRC_DIRECTORY = '/../../../../../';

    public static function getSystemFileContent(string $pattern): array
    {
        return DataHandler::mergeArray(
            self::getContents(
                $pattern,
                __DIR__ . self::SYSTEM_BUNDLE_DIRECTORY
            ),
            self::getContents(
                $pattern,
                __DIR__ . self::BUNDLE_DIRECTORY
            )
        );
    }

    public static function getTemplateFileContent(string $pattern, string $path): array
    {
        if (DataHandler::isStringInString($pattern, '/')) {
            $patternParts = DataHandler::explodeArray('/', $pattern);
            $tempPath = '';

            for ($i = 0; $i < DataHandler::getArrayLength($patternParts) - 1; $i++) {
                $tempPath .= $patternParts[$i] . '/';
            }

            $path = $path . '/' . $tempPath;
            $pattern = DataHandler::getLastArrayElement($patternParts);
        }

        return self::getContents(
            $pattern,
            __DIR__ . self::SYSTEM_BUNDLE_DIRECTORY . 'Resources/TemplateConfigurations/' . $path
        );
    }

    public static function getOneFilePath(string $pattern, $app = false): string
    {
        $path = __DIR__ . self::SYSTEM_BUNDLE_DIRECTORY;

        $return = '';
        $finder = new Finder();

        $finder->files()->name($pattern)->in($path);

        foreach ($finder as $file) {
            $return = $file->getRealPath();
        }

        return $return;
    }

    /**
     * @param $pattern
     * @return array
     */
    public static function getOneFileData($pattern): array
    {
        return self::getContents(
            $pattern,
            __DIR__ . self::BASE_DIRECTORY
        );
    }

    /**
     * @param string $pattern
     * @param string $folder
     * @param bool $multiple
     * @return array
     */
    public static function getFilesInFolder(string $pattern, string $folder, bool $multiple = true): array
    {
        return self::getContents(
            $pattern,
            __DIR__ . self::BASE_DIRECTORY . $folder,
            $multiple
        );
    }

    /**
     * @param string $pattern
     * @param string $folder
     * @return bool|array
     */
    public static function getFileInSpecificFolder(string $pattern, string $folder)
    {
        return self::getContents(
            $pattern,
            $folder
        );
    }

    /**
     * @param string $namespace
     * @param string $mode
     * @return array
     */
    public static function getClassesInFolder(string $namespace, string $mode): array
    {
        return self::searchClasses($namespace, $mode);
    }

    /**
     * @param string $path
     * @param string $mode
     * @return array
     */
    private static function searchClasses(string $path, string $mode): array
    {
        $classes = [];
        $finder = new Finder();

        $finder->files()->name('*.php')->in($path . $mode);
        
        foreach ($finder as $file) {
            $name = DataHandler::getReplaceString('.php', '', $file->getFilename());
            $subMode = DataHandler::explodeArray('/', $file->getRelativePath())[0];
            $nameSpace = 'Smug\Core\\Service\\' . DataHandler::getReplaceString('/', '\\', $mode) . '\\' . DataHandler::getReplaceString('/', '\\', $file->getRelativePath()) . '\\' . $name;


            try {
                $functions = DataHandler::getClassFunctions($nameSpace, false);
            } catch (Exception $exception) {
                return ExceptionProvider::getException($exception);
            }

            $classes[] = [
                'id' => $nameSpace,
                'name' => $subMode . ' => ' . $name,
	            'functions' => $functions
            ];
        }

        return $classes;
    }

    /**
     * @param string $pattern
     * @param string $path
     * @param bool $multiple
     * @return array
     */
    private static function getContents(string $pattern, string $path, bool $multiple = false): array
    {
        $return = [];
        $finder = new Finder();

        $finder->files()->name($pattern)->in($path);

        foreach ($finder as $file) {
            $mappingFile = DataHandler::getFile($file->getRealPath());

            if ($multiple === true) {
                $return[] = DataHandler::getJsonDecode($mappingFile, true);
                continue;
            }

            $return = DataHandler::mergeArray(
                $return,
                DataHandler::getJsonDecode($mappingFile, true)
            );
        }

        return $return;
    }

    /**
     * @return string
     */
    public static function getFileBasePath(): string
    {
        return __DIR__ . self::BASE_DIRECTORY;
    }

    /**
     * @return string
     */
    public static function getSrcPath(): string
    {
        return __DIR__ . self::SRC_DIRECTORY;
    }
}
