<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SmugAuthRedirect extends AbstractExtension
{
    public function __construct(
        private Context $context
    ) {}

    public function getFunctions()
    {
        return [
            new TwigFunction('redirect_if_not_logged_in', [$this, 'redirectIfNotLoggedIn']),
        ];
    }

    public function redirectIfNotLoggedIn(string $url, int $statusCode = 302): string|RedirectResponse
    {
        if (DataHandler::isEmpty($this->context->getUserArray())) {
            header('Location: ' . $url, true, $statusCode);
            die();
        }

        return '';
    }
}