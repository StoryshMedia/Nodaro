<?php

namespace Smug\Core\Entity\Connection\Query;

use Doctrine\DBAL\Connection;

/**
 * @internal
 */
class BulkInsertQuery
{
    /**
     * @var string[]
     */
    protected $columns;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param Connection $connection The connection to use for query execution.
     * @param string $table The name of the table to insert rows into.
     * @param string[] $columns The names of the columns to insert values into.
     *                          Can be left empty to allow arbitrary row inserts based on the table's column order.
     */
    public function __construct(Connection $connection, string $table, array $columns = [])
    {
        $this->connection = $connection;
        $this->table = $connection->quoteIdentifier($table);
        $this->columns = $columns;
    }

    /**
     * Render the bulk insert statement as string.
     */
    public function __toString(): string
    {
        return $this->getSQL();
    }

    /**
     * Adds a set of values to the bulk insert query to be inserted as a row into the specified table.
     *
     * @param array $values The set of values to be inserted as a row into the table.
     *                      If no columns have been specified for insertion, this can be
     *                      an arbitrary list of values to be inserted into the table.
     *                      Otherwise the values' keys have to match either one of the
     *                      specified column names or indexes.
     * @param array $types The types for the given values to bind to the query.
     *                     If no columns have been specified for insertion, the types'
     *                     keys will be matched against the given values' keys.
     *                     Otherwise the types' keys will be matched against the
     *                     specified column names and indexes.
     *                     Non-matching keys will be discarded, missing keys will not
     *                     be bound to a specific type.
     *
     * @throws \InvalidArgumentException if columns were specified for this query
     *                                   and either no value for one of the specified
     *                                   columns is given or multiple values are given
     *                                   for a single column (named and indexed) or
     *                                   multiple types are given for a single column
     *                                   (named and indexed).
     */
    public function addValues(array $values, array $types = [])
    {
        $valueSet = [];

        if (empty($this->columns)) {
            foreach ($values as $index => $value) {
                $this->parameters[] = $value;
                $this->types[] = $types[$index] ?? null;
                $valueSet[] = '?';
            }

            $this->values[] = $valueSet;

            return;
        }

        foreach ($this->columns as $index => $column) {
            $namedValue = isset($values[$column]) || array_key_exists($column, $values);
            $positionalValue = isset($values[$index]) || array_key_exists($index, $values);

            if (!$namedValue && !$positionalValue) {
                throw new \InvalidArgumentException(
                    sprintf('No value specified for column %s (index %d).', $column, $index),
                    1476049651
                );
            }

            if ($namedValue && $positionalValue && $values[$column] !== $values[$index]) {
                throw new \InvalidArgumentException(
                    sprintf('Multiple values specified for column %s (index %d).', $column, $index),
                    1476049652
                );
            }

            $this->parameters[] = $namedValue ? $values[$column] : $values[$index];
            $valueSet[] = '?';

            $namedType = isset($types[$column]);
            $positionalType = isset($types[$index]);

            if ($namedType && $positionalType && $types[$column] !== $types[$index]) {
                throw new \InvalidArgumentException(
                    sprintf('Multiple types specified for column %s (index %d).', $column, $index),
                    1476049653
                );
            }

            if ($namedType) {
                $this->types[] = $types[$column];

                continue;
            }

            if ($positionalType) {
                $this->types[] = $types[$index];

                continue;
            }

            $this->types[] = null;
        }

        $this->values[] = $valueSet;
    }

    /**
     * Executes this INSERT query using the bound parameters and their types.
     *
     * @return int The number of affected rows.
     *
     * @throws \LogicException if this query contains more rows than acceptable
     *                         for a single INSERT statement by the underlying platform.
     */
    public function execute(): int
    {
        return $this->connection->executeStatement($this->getSQL(), $this->parameters, $this->types);
    }

    /**
     * Returns the parameters for this INSERT query being constructed indexed by parameter index.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter types for this INSERT query being constructed indexed by parameter index.
     */
    public function getParameterTypes(): array
    {
        return $this->types;
    }

    /**
     * Returns the SQL formed by the current specifications of this INSERT query.
     *
     *
     * @throws \LogicException if no values have been specified yet.
     */
    public function getSQL(): string
    {
        if (empty($this->values)) {
            throw new \LogicException(
                'You need to add at least one set of values before generating the SQL.',
                1476049702
            );
        }

        $connection = $this->connection;
        $columnList = '';

        if (!empty($this->columns)) {
            $columnList = sprintf(
                ' (%s)',
                implode(
                    ', ',
                    array_map(
                        static function ($column) use ($connection) {
                            return $connection->quoteIdentifier($column);
                        },
                        $this->columns
                    )
                )
            );
        }

        return sprintf(
            'INSERT INTO %s%s VALUES (%s)',
            $this->table,
            $columnList,
            implode(
                '), (',
                array_map(
                    static function (array $valueSet) {
                        return implode(', ', $valueSet);
                    },
                    $this->values
                )
            )
        );
    }
}
