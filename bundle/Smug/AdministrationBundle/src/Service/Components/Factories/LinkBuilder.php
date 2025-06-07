<?php

namespace Smug\AdministrationBundle\Service\Components\Factories;

use Smug\AdministrationBundle\Interface\View\ViewInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class LinkBuilder {
    public static function build(ViewInterface $view): ViewInterface {
        $type = $view->getConfig()['type'];
        $buildFunction = 'build' . DataHandler::getFirstCapitalUpper($type);

        return self::$buildFunction($view);
    }

    public static function buildList(ViewInterface $view): ViewInterface {
        $model = $view->getConfig()['model'];
        $classArray = DataHandler::getLinkPartsFromClassString($model);

        $urls = [
            'api' => [
                'get' => self::getApiUrl($classArray) . '/paginated'
            ],
            'administration' => []
        ];

        if ($view->getConfig()['listConfig']['url']['add'] ?? false === true) {
            $urls['administration']['add'] = self::getAdministrationUrl($classArray) . '/add';
        }

        if ($view->getConfig()['listConfig']['url']['detail'] ?? false === true) {
            $urls['administration']['detail'] = self::getAdministrationUrl($classArray) . '/detail/';
        }

        $listConfig = $view->getConfig()['listConfig'] ?? [];
        $listConfig['url'] = $urls;
        $view->addConfigItem('listConfig', $listConfig);

        return $view;
    }

    public static function buildDetail(ViewInterface $view): ViewInterface {
        if (($view->getConfig()['model'] ?? false) === false) {
            return $view;
        }

        $model = $view->getConfig()['model'];
        $classArray = DataHandler::getLinkPartsFromClassString($model);

        $urls = [
            'api' => [
                'get' => $view->getConfig()['url']['customGet'] ?? false ? $view->getConfig()['url']['customGet'] : self::getApiUrl($classArray) . '/'
            ],
            'administration' => [
                'back' => self::getAdministrationUrl($classArray) . '/list'
            ]
        ];

        if ($view->getConfig()['url']['save'] ?? false === true) {
            $urls['api']['save'] = self::getApiUrl($classArray) . '/save';
        }
        if ($view->getConfig()['url']['delete'] ?? false === true) {
            $urls['api']['delete'] = self::getApiUrl($classArray) . '/delete';
        }

        $config = $view->getConfig();
        $config['url'] = $urls;
        $view->setConfig($config);

        return $view;
    }

    public static function buildAdd(ViewInterface $view): ViewInterface {
        $model = $view->getConfig()['model'];
        $classArray = DataHandler::getLinkPartsFromClassString($model);

        $urls = [
            'api' => [
            ],
            'administration' => [
                'back' => self::getAdministrationUrl($classArray) . '/list',
                'detail' => self::getAdministrationUrl($classArray) . '/detail/'
            ]
        ];

        if ($view->getConfig()['url']['save'] ?? false === true) {
            $urls['api']['save'] = self::getApiUrl($classArray) . '/add';
        }

        $config = $view->getConfig();
        $config['url'] = $urls;
        $view->setConfig($config);

        return $view;
    }

    private static function getApiUrl(array $classArray): string
    {
        $link = '/be/api/';

        $link .= DataHandler::getLowerString($classArray['namespace']) . '/';
        $link .= DataHandler::getSnakeCaseString($classArray['bundle']) . '/';
        $link .= DataHandler::getSnakeCaseString($classArray['model']);

        return $link;
    }

    private static function getAdministrationUrl(array $classArray): string
    {
        $link = '/admin/';

        $link .= DataHandler::getLowerString($classArray['namespace']) . '/';
        $link .= DataHandler::getSnakeCaseString($classArray['bundle']) . '/';
        $link .= DataHandler::getSnakeCaseString($classArray['model']);

        return $link;
    }
}
