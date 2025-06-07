<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\Backend\Data\BackendViewProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\Backend\BackendDataProviderInterface;

trait ViewDataProviderTrait
{
    public function getViewProviderClass(): BackendDataProviderInterface
    {
        /** @var BackendDataProviderInterface $provider */
        $provider = ServiceGenerationFactory::createInstance(BackendViewProvider::class);
        return $provider;
    }

    public function getProviderFunction(string $view): string
    {
        return 'provide' . DataHandler::getFirstCapitalUpper($view);
    }

    public function getConstantsClass(string $namespace, string $bundle, string $model): string
    {
        $class = DataHandler::getFirstCapitalUpper($namespace);

        $class .= '\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($bundle));

        if ($class !== 'Smug\\Core') {
            $class .= 'Bundle';
        }

        $class .= '\Constants\Views\Backend\\';
        $class .= DataHandler::getFirstCapitalUpper(DataHandler::getCamelCaseString($model));
        $class .= 'Constants';

        return $class;
    }
}