<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
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
        if (DataHandler::isEmpty($this->context->getUser())) {
            return false;
        }

        return true;
    }
}