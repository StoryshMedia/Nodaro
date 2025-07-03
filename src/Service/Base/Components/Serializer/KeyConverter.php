<?php

namespace Smug\Core\Service\Base\Components\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class KeyConverter implements NameConverterInterface
{
    public function normalize(string $propertyName): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName))));
    }

    public function denormalize(string $propertyName): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($propertyName)));
    }
}
