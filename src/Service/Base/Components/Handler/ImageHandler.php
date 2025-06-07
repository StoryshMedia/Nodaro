<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Smug\Core\Service\Base\Components\Provider\DataProvider\DefaultImageProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileProvider;

class ImageHandler
{
    public static function getDefaultImage(array $images, string $type, string $bundle, string $area) :string
    {
        if (DataHandler::getArrayLength($images) < 1) {
            return DefaultImageProvider::getDefaultImage($bundle, $type, $area);
        }

        if (DataHandler::isString($images[0])) {
            $images = [
                [
                    'media' => [
                        'path' => $images[0]
                    ]
                ]
            ];
        }

        if (!DataHandler::doesKeyExists('path', $images[0]['media'])) {
            $images[0]['media']['path'] = '';
        }

        if (!DataHandler::doesFileExist($images[0]['media']['path'])) {
            return DefaultImageProvider::getDefaultImage($bundle, $type, $area);
        }

        if (DataHandler::getSubString($images[0]['media']['path'], 0, 1) !== '/') {
            $images[0]['media']['path'] = '/' . $images[0]['media']['path'];
        }

        return FileProvider::checkForImage($images[0]['media']['path']);
    }
}
