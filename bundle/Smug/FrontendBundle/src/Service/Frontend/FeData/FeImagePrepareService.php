<?php

namespace Smug\FrontendBundle\Service\Frontend\FeData;

use Intervention\Image\ImageManagerStatic as Image;
use Smug\Core\Context\Context;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Entity\Media\MediaThumbnail;
use Smug\Core\Service\Base\Components\Generator\ThumbnailGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\MediaProvider;
use Smug\Core\Service\Base\Components\Uploader\UploaderFactory;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Service\UpdateBaseService;

class FeImagePrepareService extends UpdateBaseService
{
    const VIEWPORT_SUFFIXES = [
        '_mobile',
        '_desktop'
    ];

    public function image(Media $media, Context $context): bool
    {
        /** @var UploaderFactory $uploaderFactory */
        $uploaderFactory = ServiceGenerationFactory::createInstance(UploaderFactory::class);

        foreach (MediaProvider::getThumbnails() as $thumbnailKey => $thumbnail) {
            ThumbnailGenerator::generate(
                $thumbnailKey,
                $thumbnail,
                $media,
                $uploaderFactory,
                $context->getEntityManager(),
                $context->getProjectDir()
            );
        }
        return true;
    }

    public function transform(string $fileName, string $filePath): bool
    {
        Image::configure(['driver' => 'imagick']);

        Image::make($filePath)->encode('webp', 90)->save(
            DataHandler::getReplaceString('.jpg', '', $filePath) . '.webp'
        );

        foreach (self::VIEWPORT_SUFFIXES as $suffix) {
            $fileName = DataHandler::getReplaceString($suffix, '', $fileName);
        }
        /** @var MediaThumbnail $thumbnail */
        $thumbnail = $this->em->getRepository(MediaThumbnail::class)->findOneBy(['file' => $fileName]);

        if (DataHandler::isEmpty($thumbnail)) {
            return true;
        }

        $thumbnail->__set('extension', 'webp');

        $this->em->persist($thumbnail);
        $this->em->flush();

        return true;
    }
}
