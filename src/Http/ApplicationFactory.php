<?php

namespace Smug\Core\Http;

use Smug\Core\Http\Foundation\Request;
use Smug\Core\Kernel;

class ApplicationFactory
{
    public static function createAndRun(): void
    {
        self::handleDebug();
        self::setSecurityAspects();
        $kernel = self::createKernel();
        self::buildContainer($kernel);
        self::runRequest($kernel);
    }

    protected static function handleDebug(): void 
    {
        if ($_SERVER['APP_DEBUG']) {
            umask(0000);
            \Symfony\Component\ErrorHandler\Debug::enable();
        }
    }

    protected static function setSecurityAspects(): void
    {
        if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
            Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_FORWARDED ^ Request::HEADER_X_FORWARDED_HOST);
        }
        
        if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
            Request::setTrustedHosts([$trustedHosts]);
        }
    }

    protected static function createKernel(): Kernel
    {
        $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
        $kernel->boot();
        
        return $kernel;
    }

    protected static function buildContainer(Kernel $kernel): void
    {
        $container = $kernel->getContainer();
        \Smug\Core\Service\Base\Factory\ServiceGenerationFactory::init($container);
    }

    protected static function runRequest(Kernel $kernel): void
    {
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }
}
