<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Util\HtmlSanitizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugTruncate extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getTruncateString', [$this, 'getTruncateString']),
        ];
    }

    public function getTruncateString(string $string, int $length = 255, string $suffix = '', bool $sanitize = false): mixed
    {
        if ($sanitize) {
            $sanitizer = new HtmlSanitizer();
            $string = $sanitizer->sanitize((string) $string, [], false);
        }

        return DataHandler::truncateString($string, $length, $suffix);
    }
}