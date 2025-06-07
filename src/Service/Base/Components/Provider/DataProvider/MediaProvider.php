<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

class MediaProvider
{
    const THUMBNAIL_VARIANTS = [
        [
            'viewport' => 'desktop',
            'variant' => 'list'
        ],
        [
            'viewport' => 'mobile',
            'variant' => 'list'
        ],
        [
            'viewport' => 'desktop',
            'variant' => 'detail'
        ],
        [
            'viewport' => 'mobile',
            'variant' => 'detail'
        ]
    ];

    const THUMBNAILS = [
        'list' => [
            'mobile' => [
                'height' => 400,
                'width' => 300,
                'ratio' => 0.75
            ],
            'desktop' => [
                'height' => 512,
                'width' => 384,
                'ratio' => 0.75
            ]
        ],
        'detail' => [
            'mobile' => [
                'height' => 400,
                'width' => 300,
                'ratio' => 0.75
            ],
            'desktop' => [
                'height' => 640,
                'width' => 480,
                'ratio' => 0.75
            ]
        ]
    ];

    const HORIZONTAL_THUMBNAILS = [
        'list' => [
            'mobile' => [
                'height' => 300,
                'width' => 400,
                'ratio' => 0.75
            ],
            'desktop' => [
                'height' => 384,
                'width' => 512,
                'ratio' => 0.75
            ]
        ],
        'detail' => [
            'mobile' => [
                'height' => 300,
                'width' => 400,
                'ratio' => 0.75
            ],
            'desktop' => [
                'height' => 480,
                'width' => 640,
                'ratio' => 0.75
            ]
        ]
    ];

    public static function getThumbnails(bool $horizontal = false): array
    {
        return ($horizontal === false) ? self::THUMBNAILS : self::HORIZONTAL_THUMBNAILS;
    }

    public static function getThumbnailVariants(): array
    {
        return self::THUMBNAIL_VARIANTS;
    }
}
