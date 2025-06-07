<?php

namespace Smug\Core\Cache\Backend;

interface PhpCapableBackendInterface extends BackendInterface
{
    /**
     * @param string $entryIdentifier An identifier which describes the cache entry to load
     * @return mixed Potential return value from the include operation
     */
    public function requireOnce($entryIdentifier);
}
