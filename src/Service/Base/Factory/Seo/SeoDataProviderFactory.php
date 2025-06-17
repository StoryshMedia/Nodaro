<?php

namespace Smug\Core\Service\Base\Factory\Seo;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;
use Exception;

class SeoDataProviderFactory
{
    public static function getProvider(string $mode): ?SeoDataProviderInterface
    {
        try {
            $namespace = '\\Smug\Core\\Service\\Base\\Factory\\Seo\\Provider\\';
            $class = $namespace . DataHandler::getFirstCapitalUpper($mode) . 'Provider';

            return ServiceGenerationFactory::createInstance($class);
        } catch (Exception $exception) {
            return null;
        }
    }
}
