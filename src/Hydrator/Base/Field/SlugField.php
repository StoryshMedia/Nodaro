<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Entity\Base\Attribute\SlugSource;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Preparer\Slugify\Slugify;

class SlugField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        $attributes = DataHandler::explodeArray(',', self::getSlugSources($config));
        $slugPeaces = '';

        foreach ($attributes as $attribute) {
            $attribute = DataHandler::getReplaceString(' ', '', $attribute);
            $slugPeaces .= ($slugPeaces === '') ? $data[$attribute] : ' ' . $data[$attribute];
        }

        return Slugify::slugify($slugPeaces);
    }

    private static function getSlugSources($config)
    {
        foreach ($config as $attribute) {
            if ($attribute->getName() === SlugSource::class) {
                return $attribute->getArguments()[0];
            }
        }
    }
}