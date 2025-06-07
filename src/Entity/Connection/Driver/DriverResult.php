<?php

namespace Smug\Core\Entity\Connection\Driver;

use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\DBAL\Driver\Result as ResultInterface;

class DriverResult implements ResultInterface
{
    private \PDOStatement $statement;

    /**
     * @internal The result can be only instantiated by its driver connection or statement.
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNumeric()
    {
        return $this->fetch(\PDO::FETCH_NUM);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAssociative()
    {
        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchOne()
    {
        return $this->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAllNumeric(): array
    {
        return $this->fetchAll(\PDO::FETCH_NUM);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAllAssociative(): array
    {
        return $this->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchFirstColumn(): array
    {
        return $this->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function rowCount(): int
    {
        try {
            return $this->statement->rowCount();
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    public function columnCount(): int
    {
        try {
            return $this->statement->columnCount();
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    public function free(): void
    {
        $this->statement->closeCursor();
    }

    /**
     * @return mixed|false
     *
     * @throws Exception
     */
    private function fetch(int $mode)
    {
        try {
            $result = $this->statement->fetch($mode);
            $result = $this->mapResourceToString($result);
            return $result;
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    /**
     * @return list<mixed>
     *
     * @throws Exception
     */
    private function fetchAll(int $mode): array
    {
        try {
            $data = $this->statement->fetchAll($mode);
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }

        assert(is_array($data));
        return array_map([$this, 'mapResourceToString'], $data);
    }

    /**
     * Map resources to string like is done for e.g. in mysqli driver
     *
     * @param mixed $record
     * @return mixed
     */
    protected function mapResourceToString($record)
    {
        if (is_array($record)) {
            foreach ($record as $k => $value) {
                if (is_resource($value)) {
                    $record[$k] = stream_get_contents($value);
                }
            }
        }

        return $record;
    }
}
