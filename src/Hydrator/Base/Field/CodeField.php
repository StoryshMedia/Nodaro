<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Service\Base\Components\Util\HtmlSanitizer;

class CodeField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        $sanitizer = new HtmlSanitizer();

        return $sanitizer->sanitize((string) $data[$key], [], false, $key);
    }
}