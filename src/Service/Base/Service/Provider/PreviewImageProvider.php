<?php

namespace Smug\Core\Service\Base\Service\Provider;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;

/**
 * Class PreviewImageProvider
 * @package Smug\Core\Service\Base\Service\Provider
 */
class PreviewImageProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function provide(array $config): array
    {
        if ($config['previewImage'] !== null) {
            $arPublicationImage = $config['previewImage']->toArray();
            if (DataHandler::doesMethodExist($config['previewImage'], 'getMedia')) {
                $thumbnails = ArrayProvider::getObjectsAsArray($config['previewImage']->getMedia()->__get('thumbnails'));
            } else {
                $thumbnails = ArrayProvider::getObjectsAsArray($config['previewImage']->__get('thumbnails'));
            }
            $viewportThumbnails = [];

            foreach ($thumbnails as $thumbnail) {
                $viewportThumbnails[$thumbnail['viewport']][$thumbnail['variant']] = $thumbnail;
            }

            $arPublicationImage['media']['thumbnails'] = $viewportThumbnails;
            $image = $arPublicationImage;
        } else {
            $mediaRepository = $config['em']->getRepository(Media::class);
            $randomFallbackNumber = DataHandler::getRandomPosition(1, 5);

            /** @var Media $fallbackImage */
            $fallbackImage = $mediaRepository->findOneBy(['file' => 'fallback_0' . $randomFallbackNumber]);
            $arPublicationImage = ['media' => $fallbackImage->toArray()];
            $thumbnails = ArrayProvider::getObjectsAsArray($fallbackImage->__get('thumbnails'));
            $viewportThumbnails = [];

            foreach ($thumbnails as $thumbnail) {
                $viewportThumbnails[$thumbnail['viewport']][$thumbnail['variant']] = $thumbnail;
            }

            $arPublicationImage['media']['thumbnails'] = $viewportThumbnails;
            $image = $arPublicationImage;
        }

        return $image;
    }
}
