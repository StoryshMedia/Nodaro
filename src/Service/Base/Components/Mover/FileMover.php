<?php

namespace Smug\Core\Service\Base\Components\Mover;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;

class FileMover
{
    public function moveAttachment(array $attachmentData, $subFolder, $basePath)
    {
        $modelFolder = DataHandler::getMd5Hash(
            $attachmentData['model']['id'] . $attachmentData['model']['name']
        );

        $newPath = DataHandler::getReplaceString(
            '_temp',
            $modelFolder,
            $attachmentData['file']
        );

        DataHandler::makeDir(
            $basePath . $subFolder . $modelFolder
        );

        DataHandler::moveFile(
            $basePath . $attachmentData['file'],
            $basePath . $newPath
        );

        return [
            'success' => true,
            'src' => PathProvider::getHost() . '/' . $newPath
        ];
    }
}
