<?php

namespace Smug\Core\Service\Base\Service\Provider;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;

class PreviewImageProvider implements ProviderInterface
{
    public static function provide(array $config, EntitySerializer $serializer): array
    {
        if ($config['previewImage'] !== null) {
            $arPublicationImage = $config['previewImage']->toArray();
            if (DataHandler::doesMethodExist($config['previewImage'], 'getMedia')) {
                $thumbnails = ArrayProvider::getObjectsAsArray($config['previewImage']->getMedia()->__get('thumbnails'), $serializer);
            } else {
                $thumbnails = ArrayProvider::getObjectsAsArray($config['previewImage']->__get('thumbnails'), $serializer);
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
            $arPublicationImage = ['media' => $serializer->serialize($fallbackImage)];
            $thumbnails = ArrayProvider::getObjectsAsArray($fallbackImage->__get('thumbnails'), $serializer);
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
