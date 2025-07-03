<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Util\HtmlSanitizer;

class TextField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (!DataHandler::doesKeyExists($key, $data)) {
            return self::getDefaultValue($config);
        }

        $sanitizer = new HtmlSanitizer();

        if (DataHandler::isArray($data[$key])) {
            $data[$key] = DataHandler::getSerialize($data[$key]);
        }

        return $sanitizer->sanitize((string) $data[$key], [], false, $key);
    }
    
    private static function getDefaultValue(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === DefaultValue::class) {
                return $attribute->getArguments()[0];
            }
        }

        return '';
    }
}