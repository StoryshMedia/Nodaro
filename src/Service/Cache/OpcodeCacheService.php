<?php

namespace Smug\Core\Service\Cache;

class OpcodeCacheService
{
    /**
     * @return array Array filled with supported and active opcaches
     */
    public function getAllActive(): array
    {
        $supportedCaches = [
            'OPcache' => [
                'active' => extension_loaded('Zend OPcache') && ini_get('opcache.enable') === '1',
                'version' => phpversion('Zend OPcache'),
                'warning' => self::isClearable() ? false : 'Either opcache_invalidate or opcache_reset are disabled in this installation. Clearing will not work.',
                'clearCallback' => static function ($fileAbsPath) {
                    if (self::isClearable()) {
                        if ($fileAbsPath !== null) {
                            opcache_invalidate($fileAbsPath);
                        } else {
                            opcache_reset();
                        }
                    }
                },
            ],
        ];
        $activeCaches = [];
        foreach ($supportedCaches as $opcodeCache => $properties) {
            if ($properties['active']) {
                $activeCaches[$opcodeCache] = $properties;
            }
        }
        return $activeCaches;
    }

    /**
     * @param string|null $fileAbsPath The file as absolute path to be cleared or NULL to clear completely.
     */
    public function clearAllActive(string $fileAbsPath = null): void
    {
        foreach ($this->getAllActive() as $properties) {
            $callback = $properties['clearCallback'];
            $callback($fileAbsPath);
        }
    }

    protected static function isClearable(): bool
    {
        $disabled = explode(',', (string)ini_get('disable_functions'));
        return function_exists('opcache_invalidate')
            && function_exists('opcache_reset')
            && !(in_array('opcache_invalidate', $disabled, true) || in_array('opcache_reset', $disabled, true));
    }
}
