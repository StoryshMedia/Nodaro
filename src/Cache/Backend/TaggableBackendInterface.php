<?php

namespace Smug\Core\Cache\Backend;

interface TaggableBackendInterface extends BackendInterface
{
    /**
     * Removes all cache entries of this cache which are tagged by the specified tag.
     *
     * @param string $tag The tag the entries must have
     */
    public function flushByTag($tag);

    /**
     * Removes all cache entries of this cache which are tagged by any of the specified tags.
     *
     * @param string[] $tags List of tags
     */
    public function flushByTags(array $tags);

    /**
     * Finds and returns all cache entry identifiers which are tagged by the
     * specified tag
     *
     * @param string $tag The tag to search for
     * @return array An array with identifiers of all matching entries. An empty array if no entries matched
     */
    public function findIdentifiersByTag($tag);
}
