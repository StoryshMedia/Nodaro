<?php declare(strict_types=1);

namespace Smug\Core\Twig\Function;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmugFieldValue extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getFieldValue', [$this, 'getFieldValue']),
        ];
    }

    public function getFieldValue(array $fields, string $identifier): mixed
    {
        foreach ($fields as $field) {
            if ($field['identifier'] === $identifier) {
                return $field['value'];
            }
        }
        return '';
    }
}