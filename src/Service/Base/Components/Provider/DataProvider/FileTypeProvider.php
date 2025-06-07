<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;

class FileTypeProvider
{
    const UPLOAD_DIRECTORY = '/../../../../public/_uploads';

    const MEDIA_DIRECTORY = '/../../../../public/media';

    /**
     * @var integer
     */
    const MAX_FILE_SIZE = 200000000;

    /**
     * @var integer
     */
    const FE_MAX_FILE_SIZE = 2000000;

    /**
     * @var array
     */
    const FORBIDDEN_EXTENSIONS = ['exe', 'php', 'py', 'sh'];

    /**
     * @var array
     */
    const IMAGE_EXTENSIONS = ['jpg', 'png', 'gif', 'svg', 'jpeg', 'bmp', 'webp'];

    /**
     * @var array
     */
    const FILE_EXTENSIONS = ['csv', 'xml', 'json'];

    /**
     * @var array
     */
    const TEXT_EXTENSIONS = ['txt', 'doc', 'docx', 'rtf', 'pdf'];

    /**
     * @var array
     */
    const ARCHIVE_EXTENSIONS = ['rar', 'zip', 'arc', 'arj', 'lha', 'uue'];

    /**
     * @var array
     */
    const VIDEO_EXTENSIONS = ['mpeg', 'mpg', 'mp4', 'avi', 'mov', 'swf'];

    public static function getFilesByExtension(string $extension, string $type): array
    {
        $result = [];

        $finder = new Finder();

        $finder->files()->name('*.' . $extension)->in(__DIR__ . self::UPLOAD_DIRECTORY)->exclude('thumbnails');

        foreach ($finder as $file) {
            $path = DataHandler::getSubString($file->getRealPath(), DataHandler::isCharInString('/_uploads', $file->getRealPath()));

            $result[] = [
                'name' => $file->getFilename(),
                'path' => PathProvider::getHost() . $path,
                'type' => $type,
                'extension' => $extension,
                'size' => $file->getSize()
            ];
        }

        $finder = new Finder();

        $finder->files()->name('*.' . $extension)->in(__DIR__ . self::MEDIA_DIRECTORY)->exclude('thumbnails');

        foreach ($finder as $file) {
            $path = DataHandler::getSubString($file->getRealPath(), DataHandler::isCharInString('/media', $file->getRealPath()));

            $result[] = [
                'name' => $file->getFilename(),
                'path' => PathProvider::getHost() . $path,
                'type' => $type,
                'size' => $file->getSize()
            ];
        }

        return $result;
    }
}
