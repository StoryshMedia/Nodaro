<?php

namespace Smug\Core\Entity\Connection\Query;

use Smug\Core\Entity\Connection\Connection;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class QueryHelper
{
    /**
     * Takes an input, possibly prefixed with ORDER BY, and explodes it into
     * and array of arrays where each item consists of a fieldName and an order
     * direction.
     *
     * Each of the resulting fieldName/direction pairs can be used passed into
     * QueryBuilder::orderBy() so sort a query result set.
     *
     * @param string $input eg . "ORDER BY title, uid
     * @return array|array[] Array of arrays containing fieldName/direction pairs
     */
    public static function parseOrderBy(string $input): array
    {
        $input = preg_replace('/^(?:ORDER[[:space:]]*BY[[:space:]]*)+/i', '', trim($input)) ?: '';
        $orderExpressions = DataHandler::trimExplode(',', $input, true);

        return array_map(
            static function ($expression) {
                $fieldNameOrderArray = DataHandler::trimExplode(' ', $expression, true);
                $fieldName = $fieldNameOrderArray[0] ?? null;
                $order = $fieldNameOrderArray[1] ?? null;

                return [$fieldName, $order];
            },
            $orderExpressions
        );
    }

    /**
     * Takes an input, possibly prefixed with FROM, and explodes it into
     * and array of arrays where each item consists of a tableName and an
     * optional alias name.
     *
     * Each of the resulting pairs can be used with QueryBuilder::from()
     * to select from one or more tables.
     *
     * @param string $input eg . "FROM aTable, anotherTable AS b, aThirdTable c"
     * @return array|array[] Array of arrays containing tableName/alias pairs
     */
    public static function parseTableList(string $input): array
    {
        $input = preg_replace('/^(?:FROM[[:space:]]+)+/i', '', trim($input)) ?: '';
        $tableExpressions = DataHandler::trimExplode(',', $input, true);

        return array_map(
            static function ($expression) {
                [$tableName, $as, $alias] = array_pad(DataHandler::trimExplode(' ', $expression, true), 3, null);

                if (!empty($as) && strtolower($as) === 'as' && !empty($alias)) {
                    return [$tableName, $alias];
                }
                if (!empty($as) && empty($alias)) {
                    return [$tableName, $as];
                }
                return [$tableName, null];
            },
            $tableExpressions
        );
    }

    /**
     * Removes the prefix "GROUP BY" from the input string.
     *
     * This function should be used when you can't guarantee that the string
     * that you want to use as a GROUP BY fragment is not prefixed.
     *
     * @param string $input eg. "GROUP BY title, uid
     * @return array|string[] column names to group by
     */
    public static function parseGroupBy(string $input): array
    {
        $input = preg_replace('/^(?:GROUP[[:space:]]*BY[[:space:]]*)+/i', '', trim($input)) ?: '';

        return DataHandler::trimExplode(',', $input, true);
    }

    /**
     * Split a JOIN SQL fragment into table name, alias and join conditions.
     *
     * @param string $input eg. "JOIN tableName AS a ON a.uid = anotherTable.uid_foreign"
     * @return array assoc array consisting of the keys tableName, tableAlias and joinCondition
     */
    public static function parseJoin(string $input): array
    {
        $input = trim($input);
        $quoteCharacter = ' ';
        $matchQuotingStartCharacters = [
            '`' => '`',
            '"' => '"',
            '[' => '[]',
        ];

        // Check if the tableName is quoted
        if ($matchQuotingStartCharacters[$input[0]] ?? false) {
            $quoteCharacter .= $matchQuotingStartCharacters[$input[0]];
            $input = substr($input, 1);
            $tableName = strtok($input, $quoteCharacter);
        } else {
            $tableName = strtok($input, $quoteCharacter);
        }

        $tableAlias = (string)strtok($quoteCharacter);
        if (strtolower($tableAlias) === 'as') {
            $tableAlias = (string)strtok($quoteCharacter);
            // Skip the next token which must be ON
            strtok(' ');
            $joinCondition = strtok('');
        } elseif (strtolower($tableAlias) === 'on') {
            $tableAlias = null;
            $joinCondition = strtok('');
        } else {
            // Skip the next token which must be ON
            strtok(' ');
            $joinCondition = strtok('');
        }

        // Catch the edge case that the table name is unquoted and the
        // table alias is actually quoted. This will not work in the case
        // that the quoted table alias contains whitespace.
        $firstCharacterOfTableAlias = $tableAlias[0] ?? null;
        if ($matchQuotingStartCharacters[$firstCharacterOfTableAlias] ?? false) {
            $tableAlias = substr((string)$tableAlias, 1, -1);
        }

        $tableAlias = $tableAlias ?: $tableName;

        return ['tableName' => $tableName, 'tableAlias' => $tableAlias, 'joinCondition' => $joinCondition];
    }

    /**
     * Removes the prefixes AND/OR from the input string.
     *
     * This function should be used when you can't guarantee that the string
     * that you want to use as a WHERE fragment is not prefixed.
     *
     * @param string $constraint The where part fragment with a possible leading AND or OR operator
     * @return string The modified where part without leading operator
     */
    public static function stripLogicalOperatorPrefix(string $constraint): string
    {
        return preg_replace('/^(?:(AND|OR)[[:space:]]*)+/i', '', trim($constraint)) ?: '';
    }

    /**
     * Returns the date and time formats compatible with the given database.
     *
     * This simple method should probably be deprecated and removed later.
     *
     * @return array
     */
    public static function getDateTimeFormats()
    {
        return [
            'date' => [
                'empty' => '0000-00-00',
                'format' => 'Y-m-d',
                'reset' => null,
            ],
            'datetime' => [
                'empty' => '0000-00-00 00:00:00',
                'format' => 'Y-m-d H:i:s',
                'reset' => null,
            ],
            'time' => [
                'empty' => '00:00:00',
                'format' => 'H:i:s',
                'reset' => '00:00:00',
            ],
        ];
    }

    /**
     * Returns the date and time types compatible with the given database.
     *
     * This simple method should probably be deprecated and removed later.
     *
     * @return array
     */
    public static function getDateTimeTypes()
    {
        return [
            'date',
            'datetime',
            'time',
        ];
    }

    /**
     * Quote database table/column names indicated by {#identifier} markup in a SQL fragment string.
     * This is an intermediate step to make SQL fragments in Typoscript and TCA database agnostic.
     */
    public static function quoteDatabaseIdentifiers(Connection $connection, string $sql): string
    {
        if (str_contains($sql, '{#')) {
            $sql = preg_replace_callback(
                '/{#(?P<identifier>[^}]+)}/',
                static function (array $matches) use ($connection) {
                    return $connection->quoteIdentifier($matches['identifier']);
                },
                $sql
            );
        }

        return $sql;
    }
}
