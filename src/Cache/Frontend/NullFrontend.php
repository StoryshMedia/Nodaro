<?php

namespace Smug\Core\Cache\Frontend;

use Psr\Log\NullLogger;
use Smug\Core\Cache\Backend\NullBackend;

/**
 * @todo: Instead a factory class should be introduced that replaces this class and \TYPO3\CMS\Core\Core\Bootstrap::createCache
 */
class NullFrontend extends PhpFrontend
{
    public function __construct(string $identifier)
    {
        $backend = new NullBackend(
            '',
            [
                'logger' => new NullLogger(),
            ]
        );
        parent::__construct($identifier, $backend);
    }

    public function set($entryIdentifier, $data, array $tags = [], $lifetime = null)
    {
        // Noop
    }
}
