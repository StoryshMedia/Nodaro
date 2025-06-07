<?php

namespace Smug\Core\Entity\Connection\Schema\EventListener;

use Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Index;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;

class SchemaIndexDefinitionListener
{
    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \InvalidArgumentException
     */
    public function onSchemaIndexDefinition(SchemaIndexDefinitionEventArgs $event)
    {
        // Early  return for non-MySQL-compatible platforms
        if (!($event->getConnection()->getDatabasePlatform() instanceof MySQLPlatform)) {
            return;
        }

        $connection = $event->getConnection();
        $indexName = $event->getTableIndex()['name'];
        $sql = $event->getConnection()->getDatabasePlatform()->getListTableIndexesSQL(
            $event->getTable(),
            $event->getConnection()->getDatabase()
        );

        // check whether ORDER BY is available in SQL
        // and place the part 'AND INDEX_NAME = "SOME_INDEX_NAME"' before that
        if (str_contains($sql, 'ORDER BY')) {
            $posOfOrderBy = (int)strpos($sql, 'ORDER BY');
            $tmpSql = substr($sql, 0, $posOfOrderBy);
            $tmpSql .= ' AND ' . $connection->quoteIdentifier('INDEX_NAME') . ' = ' . $connection->quote($indexName);
            $tmpSql .= ' ' . substr($sql, $posOfOrderBy);
            $sql = $tmpSql;
            unset($tmpSql);
        } else {
            $sql .= ' AND ' . $connection->quoteIdentifier('INDEX_NAME') . ' = ' . $connection->quote($indexName);
        }

        $tableIndexes = $event->getConnection()->fetchAllAssociative($sql);

        $subPartColumns = array_filter(
            $tableIndexes,
            static function ($column) {
                return $column['Sub_Part'];
            }
        );

        if (!empty($subPartColumns)) {
            $event->setIndex($this->buildIndex($tableIndexes));
            $event->preventDefault();
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function buildIndex(array $tableIndexRows): Index
    {
        $data = null;
        foreach ($tableIndexRows as $tableIndex) {
            $tableIndex = array_change_key_case($tableIndex, CASE_LOWER);

            $tableIndex['primary'] = $tableIndex['key_name'] === 'PRIMARY';

            if (str_contains($tableIndex['index_type'], 'FULLTEXT')) {
                $tableIndex['flags'] = ['FULLTEXT'];
            } elseif (str_contains($tableIndex['index_type'], 'SPATIAL')) {
                $tableIndex['flags'] = ['SPATIAL'];
            }

            $indexName = $tableIndex['key_name'];
            $columnName = $tableIndex['column_name'];

            if ($tableIndex['sub_part'] !== null) {
                $columnName .= '(' . $tableIndex['sub_part'] . ')';
            }

            if ($data === null) {
                $data = [
                    'name' => $indexName,
                    'columns' => [$columnName],
                    'unique' => !$tableIndex['non_unique'],
                    'primary' => $tableIndex['primary'],
                    'flags' => $tableIndex['flags'] ?? [],
                    'options' => isset($tableIndex['where']) ? ['where' => $tableIndex['where']] : [],
                ];
            } else {
                $data['columns'][] = $columnName;
            }
        }

        $index = ServiceGenerationFactory::createInstance(
            Index::class,
            $data['name'],
            $data['columns'],
            $data['unique'],
            $data['primary'],
            $data['flags'],
            $data['options']
        );

        return $index;
    }
}
