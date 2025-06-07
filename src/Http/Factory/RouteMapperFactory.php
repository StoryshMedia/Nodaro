<?php

namespace Smug\Core\Http\Factory;

use Smug\Core\Entity\Connection\Connection;
use Smug\Core\Entity\Connection\ConnectionPool;
use Smug\Core\Entity\Connection\Query\QueryBuilder;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;

class RouteMapperFactory
{
    public function map(string $urlPath, array $mappingRoutes): ?array
    {
        $result = null;
        $urlParts = DataHandler::removeEmptyArrayElements(DataHandler::explodeArray('/', $urlPath));
        if (DataHandler::getArrayLength($urlParts) > 1) {
            foreach ($mappingRoutes as $mappingRoute) {
                $value = $this->resolve(
                    DataHandler::getLastArrayElement(
                        DataHandler::explodeArray('/', $urlPath)
                    ),
                    $mappingRoute
                );
    
                if (!DataHandler::isEmpty($value)) {
                    return DataHandler::mergeArray(
                        [
                            'pluginItemId' => $value['id']
                        ],
                        $mappingRoute
                    );
                }
            }
        }

        return $result;
    }

    protected function resolve(string $identifier, array $route): ?array
    {
        if (DataHandler::isEmpty($identifier)) {
            return null;
        }

        $queryBuilder = $this->createQueryBuilder($route['table']);
        $result = $queryBuilder
            ->select('*')
            ->where($queryBuilder->expr()->eq(
                $route['field'],
                $queryBuilder->createNamedParameter($identifier, Connection::PARAM_STR)
            ))
            ->executeQuery()
            ->fetchAssociative();
            
        return $result !== false ? $result : null;
    }

    protected function createQueryBuilder($tableName): QueryBuilder
    {
        $queryBuilder = ServiceGenerationFactory::createInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName)
            ->from($tableName);
        return $queryBuilder;
    }
}
