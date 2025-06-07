<?php

namespace Smug\Core\Service\Base\Components\Uploader;

use GdImage;
use Smug\Core\Context\Context;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileTypeProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\UploadErrorMessageProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderFactory
{
    public static function validateUpload(Context $context, $image): array {
        if (($image instanceof UploadedFile && $image->getError() == '0') === false) {
            return UploadErrorMessageProvider::getFileTypeError($image->getClientOriginalName());
        }

        $fileSize = ($context->getConfig()['fe'] === true) ? FileTypeProvider::FE_MAX_FILE_SIZE : FileTypeProvider::MAX_FILE_SIZE;

        if ($image->getSize() > $fileSize) {
            return UploadErrorMessageProvider::getFileSizeError($image->getClientOriginalName());
        }

        $extension = self::getFileExtension($image->getClientOriginalName());

        if (DataHandler::isInArray($extension, FileTypeProvider::FORBIDDEN_EXTENSIONS)) {
            return UploadErrorMessageProvider::getInvalidError($image->getClientOriginalName());
        }
        
        if (!DataHandler::isInArray($extension, $context->getConfigItem('allowedExtensions'))) {
            return UploadErrorMessageProvider::getInvalidError($image->getClientOriginalName());
        }

        return [
            'success' => true
        ];
    }

    public static function getFileExtension(string $orgName)
    {
        $name_array = DataHandler::explodeArray('.', $orgName);
        return DataHandler::getLowerString(
            $name_array[DataHandler::getSizeOf($name_array) - 1]
        );
    }

    public function getImageSrcPath(Media $image)
    {
        $src = $image->__get('path');

        // add backslash if it not exist
        if (DataHandler::getLastCharacterFromString($src) !== '/') {
            $src .= '/';
        }

        // add the filename to the source
        $src .= $image->__get('file');

        // add the file extension to the source
        $src .= '.' . $image->__get('extension');

        return $src;
    }

    public function getMediaPathForSave($path)
    {
        return PathProvider::getPathWithEndingBackSlash($path);
    }

    public function getMediaPath(string $media)
    {
        return PathProvider::getPathWithoutLeadingBackSlash($media);
    }

    public function getNewMediaPath(array $media, array $setting)
    {
        $mediaPath = PathProvider::getPathWithoutLeadingBackSlash(
            DataHandler::getReplaceString($media['media']['file'], '', $media['path'])
        );

        DataHandler::makeDir($mediaPath . 'thumbnails/');
        DataHandler::makeDir($mediaPath . 'thumbnails/' . $media['file'] . '/');

        $mediaPath = $mediaPath . 'thumbnails/' . $media['file'] . '/';

        return $mediaPath . $media['file'] . '_' . $setting['name'] . '.' . $media['extension'];
    }

    public function getImageObjectFromPath(string $path)
    {
        $img = DataHandler::getFile($path);
        $extension = DataHandler::getLastArrayElement(
            DataHandler::explodeArray('.', $path)
        );

        switch ($extension) {
            case 'webp':
                return imagecreatefromwebp($img);
            default:
                return imagecreatefromstring($img);
        }
    }

    /**
     * @return array
     */
    public function getImageSizes($path): array
    {
        $size = getimagesize($path);

        return [
            'width' => $size[0],
            'height' => $size[1]
        ];
    }

    public function createNewImage(GdImage $image, array $orgSize, array $newSize, $extension)
    {
        $jpegFormats = ['jpg', 'jpeg'];
        // Creates a new image with given size
        $newImage = imagecreatetruecolor($newSize['width'], $newSize['height']);

        if (DataHandler::isInArray($extension, $jpegFormats)) {
            $background = imagecolorallocate($newImage, 255, 255, 255);
            imagefill($newImage, 0, 0, $background);
        } else {
            // Disables blending
            imagealphablending($newImage, false);
        }
        imagesavealpha($newImage, true);

        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            0,
            0,
            $newSize['width'],
            $newSize['height'],
            $orgSize['width'],
            $orgSize['height']
        );

        return $newImage;
    }

    public function writeNewImage($destination, GdImage $newImage, $quality, $extension)
    {
        ob_start();
        // saves the image information into a specific file extension
        switch (strtolower($extension)) {
            case 'png':
                imagepng($newImage, $destination);
                break;
            case 'gif':
                imagegif($newImage, $destination);
                break;
            default:
                imagejpeg($newImage, $destination, $quality);
                break;
        }

        ob_end_clean();

        return true;
    }
}
