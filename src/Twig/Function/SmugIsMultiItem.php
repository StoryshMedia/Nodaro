<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugIsMultiItem extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isMultiItem', [$this, 'isMultiItem']),
        ];
    }

    public function isMultiItem(array $item): bool
    {
        return $item['module']['module']['multi'] ?? false;
    }
}