<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugDebugVariable extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('debugVariable', [$this, 'debugVariable']),
        ];
    }

    public function debugVariable($debugVariable): bool
    {
        dd($debugVariable);
    }
}