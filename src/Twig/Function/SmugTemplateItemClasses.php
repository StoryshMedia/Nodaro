<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugTemplateItemClasses extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getTemplateItemClasses', [$this, 'getTemplateItemClasses']),
        ];
    }

    public function getTemplateItemClasses(?array $items, string $identifier): mixed
    {
        foreach ($items as $item) {
            if ($item['identifier'] === $identifier) {
                if (DataHandler::isArray($item['value'])) {
                    return DataHandler::implodeArray(' ', $item['value']);
                }

                return $item['value'];
            }
        }
        return '';
    }
}