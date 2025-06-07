<?php

namespace Smug\Core\Entity\Connection\Driver;

use Doctrine\DBAL\Driver\Exception as ExceptionInterface;
use Doctrine\DBAL\Driver\Exception\UnknownParameterType;
use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use Doctrine\Deprecations\Deprecation;
use Smug\Core\Entity\Connection\Connection;

/**
 * @internal this implementation is not part of TYPO3's Public API.
 */
class DriverStatement implements StatementInterface
{
    private const PARAM_TYPE_MAP = [
        ParameterType::NULL => Connection::PARAM_NULL,
        ParameterType::INTEGER => Connection::PARAM_INT,
        ParameterType::STRING => Connection::PARAM_STR,
        ParameterType::ASCII => Connection::PARAM_STR,
        ParameterType::BINARY => Connection::PARAM_LOB,
        ParameterType::LARGE_OBJECT => Connection::PARAM_LOB,
        ParameterType::BOOLEAN => Connection::PARAM_BOOL,
    ];

    /** @var \PDOStatement */
    private $stmt;

    /**
     * @internal The statement can be only instantiated by its driver connection.
     */
    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        $type = $this->convertParamType($type);

        try {
            return $this->stmt->bindValue($param, $value, $type);
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed    $param
     * @param mixed    $variable
     * @param int      $type
     * @param int|null $length
     * @param mixed    $driverOptions The usage of the argument is deprecated.
     */
    public function bindParam(
        $param,
        &$variable,
        $type = ParameterType::STRING,
        $length = null,
        $driverOptions = null
    ): bool {
        if (func_num_args() > 4) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https://github.com/doctrine/dbal/issues/4533',
                'The $driverOptions argument of Statement::bindParam() is deprecated.'
            );
        }

        $type = $this->convertParamType($type);

        try {
            return $this->stmt->bindParam($param, $variable, $type, ...array_slice(func_get_args(), 3));
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null): ResultInterface
    {
        try {
            $this->stmt->execute($params);
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }

        // use TYPO3's Result object in favor of Doctrine's Result wrapper
        return new DriverResult($this->stmt);
    }

    /**
     * Converts DBAL parameter type to PDO parameter type
     *
     * @param int $type Parameter type
     *
     * @throws ExceptionInterface
     */
    private function convertParamType(int $type): int
    {
        if (!isset(self::PARAM_TYPE_MAP[$type])) {
            throw UnknownParameterType::new($type);
        }

        return self::PARAM_TYPE_MAP[$type];
    }
}
