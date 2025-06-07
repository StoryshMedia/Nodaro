<?php

namespace Smug\Core\Cache;

use Smug\Core\Cache\Event\CacheFlushEvent;
use Smug\Core\Cache\Backend\BackendInterface;
use Smug\Core\Cache\Backend\NullBackend;
use Smug\Core\Cache\Backend\TransientMemoryBackend;
use Smug\Core\Cache\Frontend\FrontendInterface;
use Smug\Core\Cache\Frontend\VariableFrontend;
use Smug\Core\Cache\Exception\DuplicateIdentifierException;
use Smug\Core\Cache\Exception\InvalidBackendException;
use Smug\Core\Cache\Exception\InvalidCacheException;
use Smug\Core\Cache\Exception\NoSuchCacheException;
use Smug\Core\Cache\Exception\NoSuchCacheGroupException;

class CacheManager
{
    /**
     * @var FrontendInterface[]
     */
    protected $caches = [];

    /**
     * @var array
     */
    protected $cacheConfigurations = [];

    /**
     * @var array
     */
    protected $cacheGroups = [];

    /**
     * @var array Default cache configuration as fallback
     */
    protected $defaultCacheConfiguration = [
        'frontend' => VariableFrontend::class,
        'options' => [],
        'groups' => ['all'],
    ];

    /**
     * @var bool
     */
    protected $disableCaching = false;

    public function __construct(bool $disableCaching = false)
    {
        $this->disableCaching = $disableCaching;
    }

    /**
     * @param array<string, array> $cacheConfigurations The cache configurations to set
     * @throws \InvalidArgumentException If $cacheConfigurations is not an array
     */
    public function setCacheConfigurations(array $cacheConfigurations)
    {
        $newConfiguration = [];
        foreach ($cacheConfigurations as $identifier => $configuration) {
            if (empty($identifier)) {
                throw new \InvalidArgumentException('A cache identifier was not set.', 1596980032);
            }
            if (!is_array($configuration)) {
                throw new \InvalidArgumentException('The cache configuration for cache "' . $identifier . '" was not an array as expected.', 1231259656);
            }
            $newConfiguration[$identifier] = $configuration;
        }
        $this->cacheConfigurations = $newConfiguration;
    }

    /**
     * @param FrontendInterface $cache The cache frontend to be registered
     * @param array $groups Cache groups to be associated to the cache
     * @throws DuplicateIdentifierException if a cache with the given identifier has already been registered.
     */
    public function registerCache(FrontendInterface $cache, array $groups = [])
    {
        $identifier = $cache->getIdentifier();
        if (isset($this->caches[$identifier])) {
            throw new DuplicateIdentifierException('A cache with identifier "' . $identifier . '" has already been registered.', 1203698223);
        }
        $this->caches[$identifier] = $cache;
        foreach ($groups as $groupIdentifier) {
            $this->cacheGroups[$groupIdentifier][] = $identifier;
        }
    }

    /**
     * @param string $identifier Identifies which cache to return
     * @return FrontendInterface The specified cache frontend
     * @throws NoSuchCacheException
     */
    public function getCache($identifier)
    {
        if ($this->hasCache($identifier) === false) {
            throw new NoSuchCacheException('A cache with identifier "' . $identifier . '" does not exist.', 1203699034);
        }
        if (!isset($this->caches[$identifier])) {
            $this->createCache($identifier);
        }
        return $this->caches[$identifier];
    }

    /**
     * @param string $identifier The identifier of the cache
     * @return bool TRUE if a cache with the given identifier exists, otherwise FALSE
     */
    public function hasCache($identifier)
    {
        return isset($this->caches[$identifier]) || isset($this->cacheConfigurations[$identifier]);
    }

    /**
     * Flushes all registered caches
     */
    public function flushCaches()
    {
        $this->createAllCaches();
        foreach ($this->caches as $cache) {
            $cache->flush();
        }
    }

    /**
     * @param string $groupIdentifier
     * @throws NoSuchCacheGroupException
     */
    public function flushCachesInGroup($groupIdentifier)
    {
        $this->createAllCaches();
        if (!isset($this->cacheGroups[$groupIdentifier])) {
            throw new NoSuchCacheGroupException('No cache in the specified group \'' . $groupIdentifier . '\'', 1390334120);
        }
        foreach ($this->cacheGroups[$groupIdentifier] as $cacheIdentifier) {
            if (isset($this->caches[$cacheIdentifier])) {
                $this->caches[$cacheIdentifier]->flush();
            }
        }
    }

    /**
     * @param string $groupIdentifier
     * @param string|array $tag Tag to search for
     * @throws NoSuchCacheGroupException
     */
    public function flushCachesInGroupByTag($groupIdentifier, $tag)
    {
        if (empty($tag)) {
            return;
        }
        $this->createAllCaches();
        if (!isset($this->cacheGroups[$groupIdentifier])) {
            throw new NoSuchCacheGroupException('No cache in the specified group \'' . $groupIdentifier . '\'', 1390337129);
        }
        foreach ($this->cacheGroups[$groupIdentifier] as $cacheIdentifier) {
            if (isset($this->caches[$cacheIdentifier])) {
                $this->caches[$cacheIdentifier]->flushByTag($tag);
            }
        }
    }

    /**
     * Flushes entries tagged by any of the specified tags in all registered
     * caches of a specific group.
     *
     * @param string $groupIdentifier
     * @param string[] $tags Tags to search for
     * @throws NoSuchCacheGroupException
     */
    public function flushCachesInGroupByTags($groupIdentifier, array $tags)
    {
        if (empty($tags)) {
            return;
        }
        $this->createAllCaches();
        if (!isset($this->cacheGroups[$groupIdentifier])) {
            throw new NoSuchCacheGroupException('No cache in the specified group \'' . $groupIdentifier . '\'', 1390337130);
        }
        foreach ($this->cacheGroups[$groupIdentifier] as $cacheIdentifier) {
            if (isset($this->caches[$cacheIdentifier])) {
                $this->caches[$cacheIdentifier]->flushByTags($tags);
            }
        }
    }

    /**
     * Flushes entries tagged by the specified tag of all registered
     * caches.
     *
     * @param string $tag Tag to search for
     */
    public function flushCachesByTag($tag)
    {
        $this->createAllCaches();
        foreach ($this->caches as $cache) {
            $cache->flushByTag($tag);
        }
    }

    /**
     * Flushes entries tagged by any of the specified tags in all registered caches.
     *
     * @param string[] $tags Tags to search for
     */
    public function flushCachesByTags(array $tags)
    {
        $this->createAllCaches();
        foreach ($this->caches as $cache) {
            $cache->flushByTags($tags);
        }
    }

    /**
     * @return string[]
     * @internal
     */
    public function getCacheGroups(): array
    {
        $groups = array_keys($this->cacheGroups);

        foreach ($this->cacheConfigurations as $config) {
            foreach ($config['groups'] ?? [] as $group) {
                if (!in_array($group, $groups, true)) {
                    $groups[] = $group;
                }
            }
        }

        return $groups;
    }

    public function handleCacheFlushEvent(CacheFlushEvent $event): void
    {
        foreach ($event->getGroups() as $group) {
            $this->flushCachesInGroup($group);
        }
    }

    /**
     * Instantiates all registered caches.
     */
    protected function createAllCaches()
    {
        foreach ($this->cacheConfigurations as $identifier => $_) {
            if (!isset($this->caches[$identifier])) {
                $this->createCache($identifier);
            }
        }
    }

    /**
     * @param string $identifier
     * @throws DuplicateIdentifierException
     * @throws InvalidBackendException
     * @throws InvalidCacheException
     */
    protected function createCache($identifier)
    {
        if (isset($this->cacheConfigurations[$identifier]['frontend'])) {
            $frontend = $this->cacheConfigurations[$identifier]['frontend'];
        } else {
            $frontend = $this->defaultCacheConfiguration['frontend'];
        }
        if (isset($this->cacheConfigurations[$identifier]['backend'])) {
            $backend = $this->cacheConfigurations[$identifier]['backend'];
        } else {
            $backend = $this->defaultCacheConfiguration['backend'];
        }
        if (isset($this->cacheConfigurations[$identifier]['options'])) {
            $backendOptions = $this->cacheConfigurations[$identifier]['options'];
        } else {
            $backendOptions = $this->defaultCacheConfiguration['options'];
        }

        if ($this->disableCaching && $backend !== TransientMemoryBackend::class) {
            $backend = NullBackend::class;
            $backendOptions = [];
        }

        // Add the cache identifier to the groups that it should be attached to, or use the default ones.
        if (isset($this->cacheConfigurations[$identifier]['groups']) && is_array($this->cacheConfigurations[$identifier]['groups'])) {
            $assignedGroups = $this->cacheConfigurations[$identifier]['groups'];
        } else {
            $assignedGroups = $this->defaultCacheConfiguration['groups'];
        }
        foreach ($assignedGroups as $groupIdentifier) {
            if (!isset($this->cacheGroups[$groupIdentifier])) {
                $this->cacheGroups[$groupIdentifier] = [];
            }
            $this->cacheGroups[$groupIdentifier][] = $identifier;
        }

        $backend = '\\' . ltrim($backend, '\\');
        $backendInstance = new $backend('production', $backendOptions);
        if (!$backendInstance instanceof BackendInterface) {
            throw new InvalidBackendException('"' . $backend . '" is not a valid cache backend object.', 1464550977);
        }

        // New used on purpose, see comment above
        $frontendInstance = new $frontend($identifier, $backendInstance);
        if (!$frontendInstance instanceof FrontendInterface) {
            throw new InvalidCacheException('"' . $frontend . '" is not a valid cache frontend object.', 1464550984);
        }

        $this->registerCache($frontendInstance);
    }
}
