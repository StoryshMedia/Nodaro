<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugGetFallbackImage extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getFallbackImage', [$this, 'getImage']),
        ];
    }

    public function getImage(): string
    {
        return '/site/img/author/list/preview/authorListPreview-' . rand(1, 26) . '.webp';
    }
}