<?php

namespace Smug\SearchBundle\Factory;

use Smug\SolrBundle\Result\SearchResultSet;

class PaginationOutputFactory
{
    private const EMPTY_RESULT = [
        'results' => [],
        'page' => 1,
        'limit' => 999,
        'absolute' => 999,
        'range' => [
            'from' => 1,
            'to' => 999
        ],
        'pages' => 999,
        'lastPage' => 999
    ];

    public static function getOutput(SearchResultSet $resultset, array $config) : array
    {
        $arResult = self::EMPTY_RESULT;
        $arResult['range'] = self::getRange($config, $resultset);

        $pages = ceil($resultset->getAllResultCount() / $config['limit']);

        return [
            'results' => $resultset->getSearchResults()->getArrayCopy(),
            'filters' => $resultset->getFacets()->getFilters($config),
            'page' => $config['page'],
            'limit' => $config['limit'],
            'absolute' => $resultset->getAllResultCount(),
            'range' => self::getRange($config, $resultset),
            'pages' => PaginationPageFactory::getPaginationPages($pages, $config['page']),
            'lastPage' => $pages
        ];

        return $arResult;
    }

    protected static function getRange(array $config, SearchResultSet $resultset): array
    {
        $rangeFrom = $config['page'] * $config['limit'];

        return [
            'from' => ($config['page'] - 1) * $config['limit'] + 1,
            'to' => ($rangeFrom < $resultset->getAllResultCount()) ? $rangeFrom : $resultset->getAllResultCount()
        ];
    }
}