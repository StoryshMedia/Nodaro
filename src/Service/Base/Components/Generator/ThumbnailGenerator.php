<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Entity\Media\MediaThumbnail;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Uploader\UploaderFactory;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManagerStatic as Image;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;

class ThumbnailGenerator
{
    public static function generateOneThumbnailFromMedia(
        array $thumbnailData,
        Media $media,
        UploaderFactory $uploaderFactory,
        string $destination,
        string $projectDir
    ): bool {
        Image::configure(['driver' => 'imagick']);
        $imageSrc = $projectDir . '/public/' . $uploaderFactory->getMediaPath($media->__get('path'));
        $imageSrc = $imageSrc . '/' . $media->__get('file') . '.' . $media->__get('extension');
        try {
            /** @var Image $imageObj */
            $imageResource = Image::make($imageSrc);
        } catch (\Exception $e) {
            return false;
        }

        if ($thumbnailData['width'] < $imageResource->getWidth()) {
            $ratio = $thumbnailData['width'] / $imageResource->getWidth();

            $newSize = [
                'width' => $imageResource->getWidth() * $ratio,
                'height' => $imageResource->getHeight()
            ];
        } else {
            $newSize = [
                'width' => $imageResource->getWidth(),
                'height' => $imageResource->getHeight()
            ];
        }

        $imageResource->resize($newSize['width'], $newSize['height'], function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        $imageResource->encode('webp', 90)->save($destination, 80);

        return true;
    }

    /**
     * @param $thumbnailKey
     * @param array $thumbnailData
     * @param BaseModel $media
     * @param UploaderFactory $uploaderFactory
     * @param EntityManagerInterface $em
     * @param string $projectDir
     * @return void
     */
    public static function generate(
        $thumbnailKey,
        array $thumbnailData,
        BaseModel $media,
        UploaderFactory $uploaderFactory,
        EntityManagerInterface $em,
        string $projectDir
    )
    {
        Image::configure(['driver' => 'imagick']);
        $imageSrc = $projectDir . '/public/' . $uploaderFactory->getMediaPath($media->__get('path'));

        $imageSrc = $imageSrc . $media->__get('file') . '.' . $media->__get('extension');
        $fileName = DataHandler::getReplaceString(' ', '', $media->__get('file'));

        if (DataHandler::doesFileExist($imageSrc)) {
            foreach ($thumbnailData as $viewport => $thumbnail) {
                /** @var Image $imageObj */
                $imageResource = Image::make($imageSrc);
                try {
                } catch (\Exception $e) {
                    continue;
                }

                $newPath = $projectDir . '/public/' . $media->__get('path') . '/thumbnails/';
                DataHandler::makeDir($projectDir . '/public/' . $uploaderFactory->getMediaPath($media->__get('path')) . '/thumbnails');
                $levelOne = DataHandler::generateRandomString(4);
                $levelTwo = DataHandler::generateRandomString(4);

                $newPath = $newPath . $levelOne;
                DataHandler::makeDir($newPath);
                $newPath = $newPath . '/' . $levelTwo;
                DataHandler::makeDir($newPath);

                $newImageDestination = $newPath . '/' . $fileName . '_' . $thumbnailKey . '_' . $viewport . '.webp';

                if ($thumbnail['width'] < $imageResource->getWidth()) {
                    $ratio = $thumbnail['width'] / $imageResource->getWidth();

                    $newSize = [
                        'width' => $imageResource->getWidth() * $ratio,
                        'height' => $imageResource->getHeight()
                    ];
                } else {
                    $newSize = [
                        'width' => $imageResource->getWidth(),
                        'height' => $imageResource->getHeight()
                    ];
                }

                $imageResource->resize($newSize['width'], $newSize['height'], function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $imageResource->encode('webp', 90)->save($newImageDestination, 80);

                $class = EntityGenerator::getGeneratedEntity(MediaThumbnail::class);
                $thumbnail = new $class();

                $thumbnail->__set('variant', $thumbnailKey);
                $thumbnail->__set('viewport', $viewport);
                $thumbnail->__set('extension', 'webp');
                $thumbnail->__set(
                    'path',
                    $media->__get('path') . '/thumbnails/' . $levelOne . '/' . $levelTwo
                );
                $thumbnail->__set('file', $fileName . '_' . $thumbnailKey);
                $thumbnail->__set('size', 0);
                $thumbnail->__set('sizeX', $newSize['width']);
                $thumbnail->__set('sizeY', $newSize['height']);
                $thumbnail->__set('media', $media);

                $em->persist($thumbnail);
                $media->__add('thumbnails', $thumbnail);
                $media->__set('optimized', true);
                $em->persist($media);
                $em->flush();

            }
        } else {
            echo "<pre>";
            var_export('Thumbnailgenerator');
            var_export($imageSrc);
            var_export($fileName);
        }
    }
}
