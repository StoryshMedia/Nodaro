<?php

namespace Smug\Core\Environment;

use Composer\InstalledVersions;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class Environment
{
    /**
     * A list of supported CGI server APIs
     * @var array
     */
    protected static $supportedCgiServerApis = [
        'fpm-fcgi',
        'cgi',
        'isapi',
        'cgi-fcgi',
        'srv', // HHVM with fastcgi
    ];

    /**
     * @var bool
     */
    protected static $cli;

    /**
     * @var bool
     */
    protected static $composerMode;

    /**
     * @var ApplicationContext
     */
    protected static $context;

    /**
     * @var string
     */
    protected static $projectPath;

    /**
     * @var string
     */
    protected static $composerRootPath;

    /**
     * @var string
     */
    protected static $publicPath;

    /**
     * @var string
     */
    protected static $currentScript;

    /**
     * @var string
     */
    protected static $os;

    /**
     * @var string
     */
    protected static $varPath;

    /**
     * @var string
     */
    protected static $configPath;

    /**
     * Sets up the Environment. Please note that this is not public API and only used within the very early
     * Set up of TYPO3, or to be used within tests. If you ever call this method in your extension, you're probably
     * doing something wrong. Never call this method! Never rely on it!
     *
     * @internal
     */
    public static function initialize(
        ApplicationContext $context,
        bool $cli,
        bool $composerMode,
        string $projectPath,
        string $publicPath,
        string $varPath,
        string $configPath,
        string $currentScript,
        string $os
    ) {
        self::$cli = $cli;
        self::$composerMode = $composerMode;
        self::$context = $context;
        self::$projectPath = $projectPath;
        self::$composerRootPath = $composerMode ? DataHandler::getCanonicalPath(InstalledVersions::getRootPackage()['install_path']) : '';
        self::$publicPath = $publicPath;
        self::$varPath = $varPath;
        self::$configPath = $configPath;
        self::$currentScript = $currentScript;
        self::$os = $os;
    }

    /**
     * Delivers the ApplicationContext object, usually defined in TYPO3_CONTEXT environment variables.
     * This is something like "Production", "Testing", or "Development" or any additional information
     * "Production/Staging".
     */
    public static function getContext(): ApplicationContext
    {
        return self::$context;
    }

    /**
     * Informs whether TYPO3 has been installed via composer or not. Typically this is useful inside the
     * Maintenance Modules, or the Extension Manager.
     */
    public static function isComposerMode(): bool
    {
        return self::$composerMode;
    }

    /**
     * Whether the current PHP request is handled by a CLI SAPI module or not.
     */
    public static function isCli(): bool
    {
        return self::$cli;
    }

    /**
     * The root path to the project. For installations set up via composer, this is the path where your
     * composer.json file is stored. For non-composer-setups, this is (due to legacy reasons) the public web folder
     * where the TYPO3 installation has been unzipped (something like htdocs/ or public/ on your webfolder).
     * However, non-composer-mode installations define an environment variable called "TYPO3_PATH_APP"
     * to define a different folder (usually a parent folder) to allow TYPO3 to access and store data outside
     * of the public web folder.
     *
     * @return string The absolute path to the project without the trailing slash
     */
    public static function getProjectPath(): string
    {
        return self::$projectPath;
    }

    /**
     * In most cases in composer-mode setups this is the same as project path.
     * However since the project path is configurable, the paths may differ.
     * In future versions this configurability will go away and this method will be removed.
     * This path is only required for some internal path handling regarding package paths until then.
     * @internal
     *
     * @return string The absolute path to the composer root directory without the trailing slash
     */
    public static function getComposerRootPath(): string
    {
        if (self::$composerMode === false) {
            throw new \BadMethodCallException('Composer root path is only available in Composer mode', 1631700480);
        }

        return self::$composerRootPath;
    }

    /**
     * The public web folder where index.php (= the frontend application) is put, without trailing slash.
     * For non-composer installations, the project path = the public path.
     */
    public static function getPublicPath(): string
    {
        return self::$publicPath;
    }

    /**
     * The folder where variable data like logs, sessions, locks, and cache files can be stored.
     * When project path = public path, then this folder is usually typo3temp/var/, otherwise it's set to
     * $project_path/var.
     */
    public static function getVarPath(): string
    {
        return self::$varPath;
    }

    /**
     * The folder where all global (= installation-wide) configuration like
     * - system/settings.php and
     * - system/additional.php
     * is put.
     * This folder usually has to be writable for TYPO3 in order to work.
     *
     * When project path = public path, then this folder is usually typo3conf/, otherwise it's set to
     * $project_path/config.
     */
    public static function getConfigPath(): string
    {
        return self::$configPath;
    }

    /**
     * The path + filename to the current PHP script.
     */
    public static function getCurrentScript(): string
    {
        return self::$currentScript;
    }

    /**
     * Helper methods to easily find occurrences, however as these properties are not computed
     * it is very possible that these methods will become obsolete in the near future.
     */
    /**
     * Previously found under typo3conf/l10n/
     * Please note that this might be gone at some point
     */
    public static function getLabelsPath(): string
    {
        if (self::$publicPath === self::$projectPath) {
            return self::getPublicPath() . '/typo3conf/l10n';
        }
        return self::getVarPath() . '/labels';
    }

    /**
     * Previously known as PATH_typo3
     * Please note that this might be gone at some point
     * @deprecated you should not rely on this method, as the backend path might change.
     */
    public static function getBackendPath(): string
    {
        trigger_error('Environment::getBackendPath() will be removed in TYPO3 v13.0.', E_USER_DEPRECATED);
        return self::getPublicPath() . '/typo3';
    }

    /**
     * Previously known as PATH_typo3 . 'sysext/'
     * Please note that this might be gone at some point
     */
    public static function getFrameworkBasePath(): string
    {
        return self::getPublicPath() . '/typo3/sysext';
    }

    /**
     * Please note that this might be gone at some point
     */
    public static function getExtensionsPath(): string
    {
        return self::getPublicPath() . '/typo3conf/ext';
    }

    /**
     * Previously known as PATH_typo3conf
     * Please note that this might be gone at some point
     *
     * The folder where global configuration like
     * - legacy LocalConfiguration.php,
     * - legacy AdditionalConfiguration.php, and
     * - PackageStates.php
     * is located.
     */
    public static function getLegacyConfigPath(): string
    {
        return self::getPublicPath() . '/typo3conf';
    }

    /**
     * Whether this TYPO3 installation runs on windows
     */
    public static function isWindows(): bool
    {
        return self::$os === 'WINDOWS';
    }

    /**
     * Whether this TYPO3 installation runs on unix (= non-windows machines)
     */
    public static function isUnix(): bool
    {
        return self::$os === 'UNIX';
    }

    /**
     * Returns true if the server is running on a list of supported CGI server APIs.
     */
    public static function isRunningOnCgiServer(): bool
    {
        return in_array(PHP_SAPI, self::$supportedCgiServerApis, true);
    }

    public static function usesCgiFixPathInfo(): bool
    {
        return !empty(ini_get('cgi.fix_pathinfo'));
    }

    /**
     * Returns the currently configured Environment information as array.
     */
    public static function toArray(): array
    {
        return [
            'context' => (string)self::getContext(),
            'cli' => self::isCli(),
            'projectPath' => self::getProjectPath(),
            'publicPath' => self::getPublicPath(),
            'varPath' => self::getVarPath(),
            'configPath' => self::getConfigPath(),
            'currentScript' => self::getCurrentScript(),
            'os' => self::isWindows() ? 'WINDOWS' : 'UNIX',
        ];
    }
}
