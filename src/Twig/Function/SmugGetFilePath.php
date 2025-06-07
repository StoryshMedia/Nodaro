<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugGetFilePath extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getFilePath', [$this, 'getFilePath']),
        ];
    }

    public function getFilePath(array $data): string
    {
        $url = $data['media']['path'] . '/' . $data['media']['file'] . '.' . $data['media']['extension'];

        if (!DataHandler::getSubString($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        return $url;
    }
}