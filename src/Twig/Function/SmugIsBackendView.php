<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Context\Context;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugIsBackendView extends AbstractExtension
{
    public function __construct(
        private Context $context
    ) {}

    public function getFunctions()
    {
        return [
            new TwigFunction('isBackendView', [$this, 'isBackendView']),
        ];
    }

    public function isBackendView(): bool
    {
        return ($this->context->getMode() === 'be');
    }
}