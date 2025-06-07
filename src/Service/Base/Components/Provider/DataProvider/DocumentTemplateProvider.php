<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;

class DocumentTemplateProvider
{
    public static function getDocumentTemplates(string $mode): array
    {
        switch ($mode) {
            default:
                $directory = '';
                break;
        }

        $count = 1;
        $result = [];
        //DataHandler::makeDir($directory);

        $finder = new Finder();
        $finder->depth('== 0');
        $finder->directories()->in(__DIR__ . $directory);

        foreach ($finder as $dir) {
            $path = $dir->getRealpath();

            $folderParts = DataHandler::explodeArray('/', $path);

            $result[] = [
                'id' => $count,
                'name' => DataHandler::getLastArrayElement($folderParts)
            ];

            $count++;
        }

        return $result;
    }
}
