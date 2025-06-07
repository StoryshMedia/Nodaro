<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\MediaProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugGetImage extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getImage', [$this, 'getImage']),
        ];
    }

    public function getImage(array $images, bool $returnOriginal = false): array|string
    {
        $mainImageData = (!DataHandler::doesKeyExists('id', $images)) ? DataHandler::getFirstArrayElement($images) : $images;

        if (!DataHandler::doesKeyExists('media', $mainImageData)) {
            return $this->getFallbackImage($returnOriginal);
        }

        if ($returnOriginal) {
            if (DataHandler::isEmpty($mainImageData['media'])) {
                return $this->getFallbackImage($returnOriginal);
            }

            return [
                'src' => DataHandler::getReplaceString('//', '/', $this->getSrcFromMainImage($mainImageData['media'])),
                'width' => $mainImageData['media']['sizeX'],
                'height' => $mainImageData['media']['sizeY']
            ];
        }
        
        $thumbnails = $mainImageData['media']['thumbnails'] ?? [];
        $result = [];

        if (DataHandler::isEmpty($thumbnails)) {
            if (DataHandler::isEmpty($mainImageData['media'])) {
                return $this->getFallbackImage();
            }
            
            $image = $this->getSrcFromMainImage($mainImageData['media']);

            foreach(MediaProvider::getThumbnailVariants() as $variant) {
                $result[$variant['viewport']][$variant['variant']] = [
                    'file' => $mainImageData['media']['file'],
                    'src' => $image
                ];
            }

            return $result;
        }

        foreach ($thumbnails as $thumbnail) {
            $image = $thumbnail;

            $image['src'] = $this->getSrcFromThumbnail($image);
            $result[$thumbnail['viewport']][$thumbnail['variant']] = $image;
        }

        return $result;
    }

    public function getSrcFromMainImage(array $image): string
    {
        $src = '/' . DataHandler::getReplaceString('//', '/', $image['path']);

        if (!DataHandler::getLastCharacterFromString($src) !== '/') {
            $src .= '/';
        }

        return $src . $image['file'] . '.' . $image['extension'];
    }

    public function getSrcFromThumbnail(array $image): string
    {
        $src = '/' . DataHandler::getReplaceString('//', '/', $image['path']);

        if (!DataHandler::getLastCharacterFromString($src) !== '/') {
            $src .= '/';
        }

        return $src . $image['file'] . '_' . $image['viewport'] . '.' . $image['extension'];
    }

    public function getFallbackImage($returnOriginal = false): array|string
    {
        $number = rand(1, 26);

        if ($returnOriginal) {
            return 'https://api.storysh.de/site/img/author/list/preview/authorListPreview-' . $number . '.webp';
        }

        return [
            'mobile' => [
                'list' => [
                    'src' => 'https://api.storysh.de/site/img/author/list/preview/authorListPreview-' . $number . '.webp',
                    'file' => 'authorListPreview-' . $number
                ],
                'detail' => [
                    'src' => 'https://api.storysh.de/site/img/author/list/preview/authorListPreview-' . $number . '.webp',
                    'file' => 'authorListPreview-' . $number
                ]
            ],
            'desktop' => [
                'list' => [
                    'src' => 'https://api.storysh.de/site/img/author/list/preview/authorListPreview-' . $number . '.webp',
                    'file' => 'authorListPreview-' . $number
                ],
                'detail' => [
                    'src' => 'https://api.storysh.de/site/img/author/list/preview/authorListPreview-' . $number . '.webp',
                    'file' => 'authorListPreview-' . $number
                ]
            ]
        ];
    }
}