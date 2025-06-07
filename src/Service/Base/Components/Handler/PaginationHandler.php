<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Base\Interfaces\ModelInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationHandler
{
    public static function getKnpPaginatedData(array $data, array $params, string $model): array
    {
        $rangeFrom = $params['page'] * $params['limit'];

        return [
            $model => self::getKnpItems($data['items'], $params),
            'page' => $params['page'],
            'limit' => $params['limit'],
            'absolute' => $data['total_count'],
            'range' => [
                'from' => ($params['page'] - 1) * $params['limit'] + 1,
                'to' => ($rangeFrom < $data['total_count']) ? $rangeFrom : $data['total_count']
            ],
            'pages' => self::getPaginationPages(ceil($data['total_count'] / $params['limit']), $params['page'])
        ];
    }

    public static function getPaginatedList(Query $query, array $params, $model)
    {
        if (!DataHandler::doesKeyExists('page', $params) || DataHandler::isEmpty($params['page'])) {
            $params['page'] = 1;
        }
        
        /** @var integer $pages */
        $pages = self::getPagesCount($query, $params['limit']);
        /** @var integer $absoluteCount */
        $absoluteCount = self::getAbsoluteCount($query);
        /** @var BaseModel[] $data */
        $data = self::paginate($query, $params, $params['limit'], $params['page']);

        $rangeFrom = $params['page'] * $params['limit'];

        return [
            $model => $data,
            'page' => $params['page'],
            'limit' => $params['limit'],
            'absolute' => $absoluteCount,
            'range' => [
                'from' => ($params['page'] - 1) * $params['limit'] + 1,
                'to' => ($rangeFrom < $absoluteCount) ? $rangeFrom : $absoluteCount
            ],
            'pages' => self::getPaginationPages($pages, $params['page'])
        ];
    }

    public static function getPaginationPages(int $pages, int $page) : array
    {
        $arPages = [
            'start' => 1,
            'preSteps' => [],
            'mainSteps' => [],
            'postSteps' => [],
            'end' => $pages
        ];
        $count = 1;

        for ($count; $count <= $pages; $count++) {
            if ($count === 1 || $count === $arPages['end']) {
                continue;
            }
            if (
                ($count - $page < 3 && $count - $page >= 0) ||
                ($count - $page >= -3 && $count - $page <= 0)
            ) {
                $arPages['mainSteps'][] = $count;
            }
        }

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        if (DataHandler::isEmpty($arPages['mainSteps'])) {
            return $arPages;
        }

        if ($arPages['mainSteps'][0] > 4) {
            $gap = $arPages['mainSteps'][0] - 1;
            $steps = self::getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['preSteps'][] = intdiv($gap, $count);
            }
        }

        if (($pages - DataHandler::getLastArrayElement($arPages['mainSteps'])) > 4) {
            $gap = $pages - DataHandler::getLastArrayElement($arPages['mainSteps']);
            $steps = self::getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['postSteps'][] = intdiv($gap, $count);
            }
        }

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        return $arPages;
    }

    public static function getPagesCount(Query $query, $pageSize = 20)
    {
        $paginator = new Paginator($query);
        $paginator->setUseOutputWalkers(false);

        return ceil($paginator->count() / $pageSize);
    }

    private static function getPaginationDivider(int $size): int
    {
        if ($size > 1000) {
            return 5;
        }

        if ($size > 100) {
            return 3;
        }

        if ($size > 10) {
            return 2;
        }

        return 2;
    }

    public static function getAbsoluteCount(Query $query)
    {
        $paginator = new Paginator($query);
        $paginator->setUseOutputWalkers(false);

        return $paginator->count();
    }

    public static function paginate(Query $query, $params = [], $pageSize = 10, $currentPage = 1)
    {
        $pageSize = (int)$pageSize;
        $currentPage = (int)$currentPage;

        if ($pageSize < 1) {
            $pageSize = 10;
        }

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $paginator = new Paginator($query);

        $results = $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($currentPage - 1))
            ->setMaxResults($pageSize)
            ->getResult();

        /** @var ModelInterface $result */
        foreach ($results as $key => $result) {
            if (DataHandler::doesKeyExists('searchFields', $params)) {
                if (DataHandler::getArrayLength($params['searchFields']) > 0) {
                    if (DataHandler::isArray($result)) {
                        $subResult = $result[0];
                        $result = DataHandler::unsetArrayElement($result, 0);

                        $results[$key] = DataHandler::mergeArray(
                            $result,
                            $subResult->getSelectedFieldsItem(
                                self::prepareSearchFields($params['searchFields'])
                            )
                        );
                    } else {
                        $results[$key] = $result->getSelectedFieldsItem(
                            self::prepareSearchFields($params['searchFields'])
                        );
                    }
                } else {
                    $results[$key] = $result->getListItem();
                }
            } else {
              $results[$key] = $result->getListItem();
            }
        }

        return $results;
    }

    private static function prepareSearchFields(array $searchFields)
    {
        $result = [];

        foreach ($searchFields as $searchField) {
            $result[] = $searchField['name'];
        }

        return $result;
    }

    private static function getKnpItems(array $items, array $params): array
    {
        $refactoredItems = [];

        foreach ($items as $item) {
            if (DataHandler::doesKeyExists('useComplete', $params) && $params['useComplete'] === true) {
                $refactoredItems[] = $item;
                continue;
            }
            if (!DataHandler::doesKeyExists('additionalField', $params)) {
                $refactoredItems[] = $item[0];
                continue;
            }

            $item[0][$params['additionalField']] = $item[$params['additionalField']];

            $refactoredItems[] = $item[0];
        }

        return $refactoredItems;
    }
}