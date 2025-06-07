<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugUser extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isLoggedIn', [$this, 'isLoggedIn']),
        ];
    }

    public function isLoggedIn(array $data): bool
    {
        if (!DataHandler::isEmpty($data['user'] ?? [])) {
            return true;
        }

        return false;
    }
}