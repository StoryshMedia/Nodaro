<?php

namespace Smug\Core\Http\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;

class ConfigurationFactory
{
    public static function getConfigurationPath(string $baseUrl): string
    {
        if (!DataHandler::proofDir(dirname(__DIR__) . '/../../config/bundle/frontend/' . $baseUrl)) {
            return '';
        }

        if (!DataHandler::proofDir(dirname(__DIR__) . '/../../config/bundle/frontend/' . $baseUrl . '/routing/')) {
            return '';
        }

        return dirname(__DIR__) . '/../../config/bundle/frontend/' . $baseUrl . '/routing/';
    }

    public static function getConfigurations(string $baseUrl): array
    {
        $configurations = [];
        $path = self::getConfigurationPath($baseUrl);

        if (DataHandler::isEmpty($path)) {
            return $configurations;
        }

        $finder = new Finder();
        $finder->files()->in($path);

        foreach ($finder as $configurationFile) {
            $configurations[self::getConfigurationName($configurationFile)] = DataHandler::getJsonDecode(
                DataHandler::getFile($configurationFile->getRealPath()),
                true
            );
        }

        return $configurations;
    }

    protected static function getConfigurationName($file): string
    {
        return DataHandler::getReplaceString('.' . $file->getExtension(), '', $file->getBaseName());
    }
}
