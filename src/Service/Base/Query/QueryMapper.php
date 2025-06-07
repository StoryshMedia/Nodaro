<?php

namespace Smug\Core\Service\Base\Query;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Class QueryMapper
 * @package Smug\Core\Service\Base\Query
 */
class QueryMapper
{
    /**
     * @param array $params
     * @param string $identifier
     * @param array $advancedFilterSettings
     * @return array
     */
    public function prepareSearchParams(array $params, string $identifier = 'name', array $advancedFilterSettings = []): array
    {
        if (!DataHandler::doesKeyExists('alphabetical', $params)) {
            $params['alphabetical'] = '';
        }

        if (!DataHandler::doesKeyExists('filter', $params)) {
            $params['filter'] = '';
        }

        if (!DataHandler::doesKeyExists('sorting', $params)) {
            $params['sorting'] = '';
        }

        if ($params['alphabetical'] !== '') {
            $params['alphabetical'] = [
                'expression' => 'c.' . $identifier . ' LIKE :alphabetical',
                'value' => $params['alphabetical']
            ];
        }

        if ($params['filter'] !== '') {
            $params['filter'] = self::clearFilters($params['filter']);

            if (DataHandler::getArrayLength($advancedFilterSettings) > 0) {
                if (DataHandler::doesKeyExists($params['filter']['value'], $advancedFilterSettings)) {
                    $params['filter']['filterData'] = $advancedFilterSettings[$params['filter']['value']];
                }
            }
        }

        if ($params['sorting'] !== '') {
            $params['sorting'] = self::getSortingMappings($params['sorting'], $params['allSortings']);

            if (!DataHandler::doesKeyExists('direction', $params['sorting']) && DataHandler::doesKeyExists('direction', $params)) {
                $params['sorting']['direction'] = $params['direction'];
            }
        }

        return $params;
    }

    /**
     * @param array $filter
     * @return array
     */
    public static function clearFilters(array $filter): array
    {
        if (!DataHandler::doesKeyExists('filterData', $filter)) {
            foreach ($filter as $filterItem) {
                if (!DataHandler::doesKeyExists('parameters', $filterItem['filterData'])) {
                    continue;
                }
        
                foreach ($filterItem['filterData']['parameters'] as $parameterKey => $parameter) {
                    if (!DataHandler::doesKeyExists('value', $parameter)) {
                        $filterItem['filterData']['parameters'] = DataHandler::unsetArrayElement(
                            $filterItem['filterData']['parameters'],
                            $parameterKey
                        );
                    }
                }
            }
        } else {
            if (!DataHandler::doesKeyExists('parameters', $filter['filterData'])) {
                return $filter;
            }
    
            foreach ($filter['filterData']['parameters'] as $parameterKey => $parameter) {
                if (!DataHandler::doesKeyExists('value', $parameter)) {
                    $filter['filterData']['parameters'] = DataHandler::unsetArrayElement(
                        $filter['filterData']['parameters'],
                        $parameterKey
                    );
                }
            }
        }

        return $filter;
    }
	
	/**
	 * @param string $filterString
	 * @param array $allFilters
	 * @return array
	 */
    public static function getFilterMappings(string $filterString, array $allFilters): array
    {
        foreach ($allFilters as $filter) {
            if ($filter['value'] === $filterString) {
                return $filter;
            }
        }

        return [];
    }
	
	/**
	 * @param string $sortingString
	 * @param array $allSortings
	 * @return array
	 */
    public static function getSortingMappings(string $sortingString, array $allSortings): array
    {
        foreach ($allSortings as $sorting) {
        	if ($sorting === null) {
        		continue;
	        }
            if ($sorting['value'] === $sortingString) {
                return $sorting;
            }
        }

        return [];
    }
}
