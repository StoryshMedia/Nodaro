<?php

namespace Smug\Core\Cache\Frontend;

use Exception;
use Smug\Core\Cache\Backend\PhpCapableBackendInterface;

class PhpFrontend extends AbstractFrontend
{
    /**
     * Constructs the cache
     *
     * @param string $identifier An identifier which describes this cache
     * @param PhpCapableBackendInterface $backend Backend to be used for this cache
     */
    public function __construct($identifier, PhpCapableBackendInterface $backend)
    {
        parent::__construct($identifier, $backend);
    }

    /**
     * Saves the PHP source code in the cache.
     *
     * @param string $entryIdentifier An identifier used for this cache entry, for example the class name
     * @param string $sourceCode PHP source code
     * @param array $tags Tags to associate with this cache entry
     * @param int $lifetime Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited lifetime.
     * @throws \InvalidArgumentException If $entryIdentifier or $tags is invalid
     * @throws InvalidDataException If $sourceCode is not a string
     */
    public function set($entryIdentifier, $sourceCode, array $tags = [], $lifetime = null)
    {
        if (!$this->isValidEntryIdentifier($entryIdentifier)) {
            throw new \InvalidArgumentException('"' . $entryIdentifier . '" is not a valid cache entry identifier.', 1264023823);
        }
        if (!is_string($sourceCode)) {
            throw new Exception('The given source code is not a valid string.', 1264023824);
        }
        foreach ($tags as $tag) {
            if (!$this->isValidTag($tag)) {
                throw new \InvalidArgumentException('"' . $tag . '" is not a valid tag for a cache entry.', 1264023825);
            }
        }
        $sourceCode = '<?php' . chr(10) . $sourceCode . chr(10) . '#';
        $this->backend->set($entryIdentifier, $sourceCode, $tags, $lifetime);
    }

    /**
     * Finds and returns a variable value from the cache.
     *
     * @param string $entryIdentifier Identifier of the cache entry to fetch
     * @return string The value
     * @throws \InvalidArgumentException if the cache identifier is not valid
     */
    public function get($entryIdentifier)
    {
        if (!$this->isValidEntryIdentifier($entryIdentifier)) {
            throw new \InvalidArgumentException('"' . $entryIdentifier . '" is not a valid cache entry identifier.', 1233057753);
        }
        return $this->backend->get($entryIdentifier);
    }
}
