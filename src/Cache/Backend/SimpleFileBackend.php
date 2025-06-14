<?php

namespace Smug\Core\Cache\Backend;

use Exception;
use Smug\Core\Cache\Frontend\FrontendInterface;
use Smug\Core\Cache\Frontend\PhpFrontend;
use Smug\Core\Environment\Environment;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Cache\OpcodeCacheService;

class SimpleFileBackend extends AbstractBackend implements PhpCapableBackendInterface
{
    public const SEPARATOR = '^';
    public const EXPIRYTIME_FORMAT = 'YmdHis';
    public const EXPIRYTIME_LENGTH = 14;
    public const DATASIZE_DIGITS = 10;
    /**
     * Directory where the files are stored
     *
     * @var string
     */
    protected $cacheDirectory = '';

    /**
     * Temporary path to cache directory before setCache() was called. It is
     * set by setCacheDirectory() and used in setCache() method which calls
     * the directory creation if needed. The variable is not used afterwards,
     * the final cache directory path is stored in $this->cacheDirectory then.
     *
     * @var string Temporary path to cache directory
     */
    protected $temporaryCacheDirectory = '';

    /**
     * A file extension to use for each cache entry.
     *
     * @var string
     */
    protected $cacheEntryFileExtension = '';

    /**
     * @var array
     */
    protected $cacheEntryIdentifiers = [];

    /**
     * @var bool
     */
    protected $frozen = false;

    /**
     * Sets a reference to the cache frontend which uses this backend and
     * initializes the default cache directory.
     *
     * @param FrontendInterface $cache The cache frontend
     * @throws Exception
     */
    public function setCache(FrontendInterface $cache)
    {
        parent::setCache($cache);
        if (empty($this->temporaryCacheDirectory)) {
            // If no cache directory was given with cacheDirectory
            // configuration option, set it to a path below var/ folder
            $temporaryCacheDirectory = Environment::getVarPath() . '/';
        } else {
            $temporaryCacheDirectory = $this->temporaryCacheDirectory;
        }
        $codeOrData = $cache instanceof PhpFrontend ? 'code' : 'data';
        $finalCacheDirectory = $temporaryCacheDirectory . 'cache/' . $codeOrData . '/' . $this->cacheIdentifier . '/';
        if (!is_dir($finalCacheDirectory)) {
            $this->createFinalCacheDirectory($finalCacheDirectory);
        }
        unset($this->temporaryCacheDirectory);
        $this->cacheDirectory = $finalCacheDirectory;
        $this->cacheEntryFileExtension = $cache instanceof PhpFrontend ? '.php' : '';
        if (strlen($this->cacheDirectory) + 23 > PHP_MAXPATHLEN) {
            throw new Exception('The length of the temporary cache file path "' . $this->cacheDirectory . '" exceeds the maximum path length of ' . (PHP_MAXPATHLEN - 23) . '. Please consider setting the temporaryDirectoryBase option to a shorter path.', 1248710426);
        }
    }

    /**
     * Sets the directory where the cache files are stored. By default it is
     * assumed that the directory is below TYPO3's Project Path. However, an
     * absolute path can be selected, too.
     *
     * This method enables to use a cache path outside of TYPO3's Project Path. The final
     * cache path is checked and created in createFinalCacheDirectory(),
     * called by setCache() method, which is done _after_ the cacheDirectory
     * option was handled.
     *
     * @param string $cacheDirectory The cache base directory. If a relative path
     * @throws Exception if the directory is not within allowed
     */
    public function setCacheDirectory($cacheDirectory)
    {
        // Skip handling if directory is a stream resource
        // This is used by unit tests with vfs:// directories
        if (DataHandler::hasProtocolAndScheme($cacheDirectory)) {
            $this->temporaryCacheDirectory = $cacheDirectory;
            return;
        }
        $documentRoot = Environment::getProjectPath() . '/';
        if ($open_basedir = ini_get('open_basedir')) {
            if (Environment::isWindows()) {
                $delimiter = ';';
                $cacheDirectory = str_replace('\\', '/', $cacheDirectory);
                if (!preg_match('/[A-Z]:/', substr($cacheDirectory, 0, 2))) {
                    $cacheDirectory = Environment::getProjectPath() . $cacheDirectory;
                }
            } else {
                $delimiter = ':';
                if ($cacheDirectory[0] !== '/') {
                    // relative path to cache directory.
                    $cacheDirectory = Environment::getProjectPath() . $cacheDirectory;
                }
            }
            $basedirs = explode($delimiter, $open_basedir);
            $cacheDirectoryInBaseDir = false;
            foreach ($basedirs as $basedir) {
                if (Environment::isWindows()) {
                    $basedir = str_replace('\\', '/', $basedir);
                }
                if ($basedir[strlen($basedir) - 1] !== '/') {
                    $basedir .= '/';
                }
                if (str_starts_with($cacheDirectory, $basedir)) {
                    $documentRoot = $basedir;
                    $cacheDirectory = str_replace($basedir, '', $cacheDirectory);
                    $cacheDirectoryInBaseDir = true;
                    break;
                }
            }
            if (!$cacheDirectoryInBaseDir) {
                throw new Exception(
                    'Open_basedir restriction in effect. The directory "' . $cacheDirectory . '" is not in an allowed path.',
                    1476045417
                );
            }
        } else {
            if ($cacheDirectory[0] === '/') {
                // Absolute path to cache directory.
                $documentRoot = '';
            }
            if (Environment::isWindows()) {
                if (!empty($documentRoot) && str_starts_with($cacheDirectory, $documentRoot)) {
                    $documentRoot = '';
                }
            }
        }
        // After this point all paths have '/' as directory separator
        if ($cacheDirectory[strlen($cacheDirectory) - 1] !== '/') {
            $cacheDirectory .= '/';
        }
        $this->temporaryCacheDirectory = $documentRoot . $cacheDirectory;
    }

    /**
     * Create the final cache directory if it does not exist.
     *
     * @param string $finalCacheDirectory Absolute path to final cache directory
     * @throws Exception If directory is not writable after creation
     */
    protected function createFinalCacheDirectory($finalCacheDirectory)
    {
        try {
            DataHandler::makeDir($finalCacheDirectory);
        } catch (\RuntimeException $e) {
            throw new Exception('The directory "' . $finalCacheDirectory . '" can not be created.', 1303669848, $e);
        }
        if (!is_writable($finalCacheDirectory)) {
            throw new Exception('The directory "' . $finalCacheDirectory . '" is not writable.', 1203965200);
        }
    }

    /**
     * Returns the directory where the cache files are stored
     *
     * @return string Full path of the cache directory
     */
    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
    }

    /**
     * Saves data in a cache file.
     *
     * @param string $entryIdentifier An identifier for this specific cache entry
     * @param string $data The data to be stored
     * @param array $tags Tags to associate with this cache entry
     * @param int $lifetime This cache backend does not support life times
     * @throws Exception if the directory does not exist or is not writable or exceeds the maximum allowed path length, or if no cache frontend has been set.
     * @throws InvalidDataException if the data to bes stored is not a string.
     * @throws \InvalidArgumentException
     */
    public function set($entryIdentifier, $data, array $tags = [], $lifetime = null)
    {
        if (!is_string($data)) {
            throw new Exception('The specified data is of type "' . gettype($data) . '" but a string is expected.', 1334756734);
        }
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1334756735);
        }
        if ($entryIdentifier === '') {
            throw new \InvalidArgumentException('The specified entry identifier must not be empty.', 1334756736);
        }
        $temporaryCacheEntryPathAndFilename = $this->cacheDirectory . DataHandler::getUniqueId() . '.temp';
        $result = file_put_contents($temporaryCacheEntryPathAndFilename, $data);
        DataHandler::fixPermissions($temporaryCacheEntryPathAndFilename);
        if ($result === false) {
            throw new Exception('The temporary cache file "' . $temporaryCacheEntryPathAndFilename . '" could not be written.', 1334756737);
        }
        $cacheEntryPathAndFilename = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        rename($temporaryCacheEntryPathAndFilename, $cacheEntryPathAndFilename);
        if ($this->cacheEntryFileExtension === '.php') {
            ServiceGenerationFactory::createInstance(OpcodeCacheService::class)->clearAllActive($cacheEntryPathAndFilename);
        }
    }

    /**
     * Loads data from a cache file.
     *
     * @param string $entryIdentifier An identifier which describes the cache entry to load
     * @return mixed The cache entry's content as a string or FALSE if the cache entry could not be loaded
     * @throws \InvalidArgumentException If identifier is invalid
     */
    public function get($entryIdentifier)
    {
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1334756877);
        }
        $pathAndFilename = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        if (!file_exists($pathAndFilename)) {
            return false;
        }
        return file_get_contents($pathAndFilename);
    }

    /**
     * Checks if a cache entry with the specified identifier exists.
     *
     * @param string $entryIdentifier
     * @return bool TRUE if such an entry exists, FALSE if not
     * @throws \InvalidArgumentException
     */
    public function has($entryIdentifier)
    {
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1334756878);
        }
        return file_exists($this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension);
    }

    /**
     * Removes all cache entries matching the specified identifier.
     * Usually this only affects one entry.
     *
     * @param string $entryIdentifier Specifies the cache entry to remove
     * @return bool TRUE if (at least) an entry could be removed or FALSE if no entry was found
     * @throws \InvalidArgumentException
     */
    public function remove($entryIdentifier)
    {
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1334756960);
        }
        if ($entryIdentifier === '') {
            throw new \InvalidArgumentException('The specified entry identifier must not be empty.', 1334756961);
        }
        $file = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    /**
     * Removes all cache entries of this cache.
     * Flushes a directory by first moving to a temporary resource, and then
     * triggering the remove process. This way directories can be flushed faster
     * to prevent race conditions on concurrent processes accessing the same directory.
     */
    public function flush()
    {
        $directory = $this->cacheDirectory;
        if (is_link($directory)) {
            // Avoid attempting to rename the symlink see #87367
            $directory = (string)realpath($directory);
        }

        if (is_dir($directory)) {
            $temporaryDirectory = rtrim($directory, '/') . '.' . DataHandler::getUniqueId('remove');
            if (rename($directory, $temporaryDirectory)) {
                DataHandler::makeDir($directory);
                clearstatcache();
                DataHandler::removeDir($temporaryDirectory, true);
            }
        }
    }

    /**
     * Checks if the given cache entry files are still valid or if their
     * lifetime has exceeded.
     *
     * @param string $cacheEntryPathAndFilename
     * @return bool
     */
    protected function isCacheFileExpired($cacheEntryPathAndFilename)
    {
        return file_exists($cacheEntryPathAndFilename) === false;
    }

    /**
     * Not necessary
     */
    public function collectGarbage() {}

    /**
     * Tries to find the cache entry for the specified identifier.
     *
     * @param string $entryIdentifier The cache entry identifier
     * @return mixed The file names (including path) as an array if one or more entries could be found, otherwise FALSE
     */
    protected function findCacheFilesByIdentifier($entryIdentifier)
    {
        $pathAndFilename = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        return file_exists($pathAndFilename) ? [$pathAndFilename] : false;
    }

    /**
     * Loads PHP code from the cache and require_onces it right away.
     *
     * @param string $entryIdentifier An identifier which describes the cache entry to load
     * @return mixed Potential return value from the include operation
     * @throws \InvalidArgumentException
     */
    public function requireOnce($entryIdentifier)
    {
        $pathAndFilename = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1282073037);
        }
        return file_exists($pathAndFilename) ? require_once $pathAndFilename : false;
    }

    /**
     * Loads PHP code from the cache and require it right away.
     *
     * @param string $entryIdentifier An identifier which describes the cache entry to load
     * @return mixed Potential return value from the include operation
     * @throws \InvalidArgumentException
     */
    public function require(string $entryIdentifier)
    {
        $pathAndFilename = $this->cacheDirectory . $entryIdentifier . $this->cacheEntryFileExtension;
        if ($entryIdentifier !== DataHandler::basename($entryIdentifier)) {
            throw new \InvalidArgumentException('The specified entry identifier must not contain a path segment.', 1532528267);
        }
        return file_exists($pathAndFilename) ? require $pathAndFilename : false;
    }
}
