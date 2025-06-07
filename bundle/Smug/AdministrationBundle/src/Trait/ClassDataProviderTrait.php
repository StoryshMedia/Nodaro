<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait ClassDataProviderTrait
{
    public function getClass(string $namespace, string $bundle, string $model): string
    {
        $class = DataHandler::getFirstCapitalUpper($namespace);

        $class .= '\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($bundle));

        if ($class !== 'Smug\\Core') {
            $class .= 'Bundle';
        }

        $class .= '\Entity\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($model));
        $class .= '\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($model));

        return $class;
    }

    public function getConstants(string $namespace, string $bundle, string $model): string
    {
        $class = DataHandler::getFirstCapitalUpper($namespace);

        $class .= '\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($bundle));

        if ($class !== 'Smug\\Core') {
            $class .= 'Bundle';
        }

        $class .= '\Constants\Config\Backend\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($model));
        $class .= 'Constants';

        return $class;
    }
}