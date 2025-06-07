<?php

namespace Smug\Core\Entity\Connection\Driver;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\PDO\Connection as DoctrineDbalPDOConnection;
use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;

/**
 * @method resource|object getNativeConnection()
 */
class DriverConnection implements ConnectionInterface
{
    protected DoctrineDbalPDOConnection $doctrineDbalPDOConnection;
    protected \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->doctrineDbalPDOConnection = new DoctrineDbalPDOConnection($connection);
    }

    public function exec(string $sql): int
    {
        return $this->doctrineDbalPDOConnection->exec($sql);
    }

    public function getServerVersion()
    {
        return $this->doctrineDbalPDOConnection->getServerVersion();
    }

    public function prepare(string $sql): StatementInterface
    {
        try {
            $stmt = $this->connection->prepare($sql);
            assert($stmt instanceof \PDOStatement);

            return new DriverStatement($stmt);
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    public function query(string $sql): ResultInterface
    {
        try {
            $stmt = $this->connection->query($sql);
            assert($stmt instanceof \PDOStatement);

            return new DriverResult($stmt);
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        return $this->doctrineDbalPDOConnection->quote($value, $type);
    }

    public function lastInsertId($name = null)
    {
        return $this->doctrineDbalPDOConnection->lastInsertId($name);
    }

    public function beginTransaction(): bool
    {
        return $this->doctrineDbalPDOConnection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->doctrineDbalPDOConnection->commit();
    }

    public function rollBack(): bool
    {
        return $this->doctrineDbalPDOConnection->rollBack();
    }

    public function getWrappedConnection(): \PDO
    {
        return $this->doctrineDbalPDOConnection->getWrappedConnection();
    }
}
