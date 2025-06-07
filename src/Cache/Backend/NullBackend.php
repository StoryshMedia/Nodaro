<?php

namespace Smug\Core\Cache\Backend;

class NullBackend extends AbstractBackend implements PhpCapableBackendInterface, TaggableBackendInterface
{
    /**
     * Acts as if it would save data
     *
     * @param string $entryIdentifier ignored
     * @param string $data ignored
     * @param array $tags ignored
     * @param int $lifetime ignored
     */
    public function set($entryIdentifier, $data, array $tags = [], $lifetime = null) {}

    /**
     * Acts as if it would enable data compression
     *
     * @param bool $compression ignored
     */
    public function setCompression($compression) {}

    /**
     * Returns False
     *
     * @param string $entryIdentifier ignored
     * @return bool FALSE
     */
    public function get($entryIdentifier)
    {
        return false;
    }

    /**
     * Returns False
     *
     * @param string $entryIdentifier ignored
     * @return bool FALSE
     */
    public function has($entryIdentifier)
    {
        return false;
    }

    /**
     * Does nothing
     *
     * @param string $entryIdentifier ignored
     * @return bool FALSE
     */
    public function remove($entryIdentifier)
    {
        return false;
    }

    /**
     * Returns an empty array
     *
     * @param string $tag ignored
     * @return array An empty array
     */
    public function findIdentifiersByTag($tag)
    {
        return [];
    }

    /**
     * Does nothing
     */
    public function flush() {}

    /**
     * Does nothing
     *
     * @param string $tag ignored
     */
    public function flushByTag($tag) {}

    /**
     * Does nothing
     */
    public function collectGarbage() {}

    /**
     * Does nothing
     *
     * @param string $identifier An identifier which describes the cache entry to load
     * @return bool
     */
    public function requireOnce($identifier)
    {
        return false;
    }

    /**
     * Does nothing
     *
     * @param string $identifier An identifier which describes the cache entry to load
     * @return bool
     */
    public function require(string $identifier)
    {
        return false;
    }
}
