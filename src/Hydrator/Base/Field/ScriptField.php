<?php

namespace Smug\Core\Hydrator\Base\Field;

use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ScriptField extends Field
{
    public static function hydrate(array $data, string $key, array $config = []): mixed
    {
        if (!DataHandler::doesKeyExists($key, $data)) {
            return self::getDefaultValue($config);
        }

        if (self::isMalicious((string) $data[$key])) {
            return self::getDefaultValue($config);
        }

        return (string) $data[$key];
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

    private static function isMalicious($string): bool {
        // Convert special HTML characters to prevent script execution
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    
        // Common patterns to detect potentially malicious code
        $patterns = [
            // Detect VBScript
            '/<\s*vbscript\b[^>]*>(.*?)<\/vbscript>/is',
            // Detect iframe inclusion
            '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            // Detect object inclusion
            '/<object\b[^>]*>(.*?)<\/object>/is',
            // Detect common SQL injection patterns
            '/(UNION|SELECT|INSERT|DELETE|UPDATE|DROP|ALTER|CREATE|REPLACE)[\s\(\;]/i',
            // Detect eval and other dangerous PHP functions
            '/\beval\s*\(/i',
            '/\bexec\s*\(/i',
            '/\bsystem\s*\(/i',
            '/\bpopen\s*\(/i',
            '/\bpassthru\s*\(/i',
            '/\bshell_exec\s*\(/i',
            '/\bproc_open\s*\(/i',
        ];
    
        // Check for each pattern
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $string)) {
                return true; // Pattern found, string is considered malicious
            }
        }
    
        return false; // No patterns found, string is considered safe
    }
}