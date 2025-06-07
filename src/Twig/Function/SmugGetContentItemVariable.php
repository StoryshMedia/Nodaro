<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugGetContentItemVariable extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getContentItemVariable', [$this, 'getContentItemVariable']),
        ];
    }

    public function getContentItemVariable(array $items, string $identifier, string $key): string
    {
        foreach ($items as $item) {
            if ($item['module']['module']['identifier'] === $identifier) {
                return DataHandler::getStringValue($item['variables'][$key]);
            }
        }

        return '';
    }
}