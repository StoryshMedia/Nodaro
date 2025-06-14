<?php

namespace Smug\Core\Entity\Connection\Driver;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use Doctrine\DBAL\Driver\Connection as DriverConnectionInterface;
use Doctrine\DBAL\Driver\PDO\Exception;

class PDOPgSql extends AbstractPostgreSQLDriver
{
    /**
     * {@inheritdoc}
     *
     * @return DriverConnectionInterface
     */
    public function connect(array $params)
    {
        $driverOptions = $params['driverOptions'] ?? [];

        if (!empty($params['persistent'])) {
            $driverOptions[\PDO::ATTR_PERSISTENT] = true;
        }

        try {
            $pdo = new \PDO(
                $this->_constructPdoDsn($params),
                $params['user'] ?? '',
                $params['password'] ?? '',
                $driverOptions,
            );

            if (defined('PDO::PGSQL_ATTR_DISABLE_PREPARES')
                && (
                    !isset($driverOptions[\PDO::PGSQL_ATTR_DISABLE_PREPARES])
                    || $driverOptions[\PDO::PGSQL_ATTR_DISABLE_PREPARES] === true
                )
            ) {
                $pdo->setAttribute(\PDO::PGSQL_ATTR_DISABLE_PREPARES, true);
            }

            /* defining client_encoding via SET NAMES to avoid inconsistent DSN support
             * - the 'client_encoding' connection param only works with postgres >= 9.1
             * - passing client_encoding via the 'options' param breaks pgbouncer support
             */
            if (isset($params['charset'])) {
                $pdo->exec('SET NAMES \'' . $params['charset'] . '\'');
            }
        } catch (\PDOException $exception) {
            throw Exception::new($exception);
        }

        return new DriverConnection($pdo);
    }

    /**
     * Constructs the Postgres PDO DSN.
     *
     * @param mixed[] $params
     *
     * @return string The DSN.
     */
    private function _constructPdoDsn(array $params)
    {
        $dsn = 'pgsql:';

        if (isset($params['host']) && $params['host'] !== '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }

        if (isset($params['port']) && $params['port'] !== '') {
            $dsn .= 'port=' . $params['port'] . ';';
        }

        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        } elseif (isset($params['default_dbname'])) {
            $dsn .= 'dbname=' . $params['default_dbname'] . ';';
        } else {
            // Used for temporary connections to allow operations like dropping the database currently connected to.
            // Connecting without an explicit database does not work, therefore "postgres" database is used
            // as it is mostly present in every server setup.
            $dsn .= 'dbname=postgres;';
        }

        if (isset($params['sslmode'])) {
            $dsn .= 'sslmode=' . $params['sslmode'] . ';';
        }

        if (isset($params['sslrootcert'])) {
            $dsn .= 'sslrootcert=' . $params['sslrootcert'] . ';';
        }

        if (isset($params['sslcert'])) {
            $dsn .= 'sslcert=' . $params['sslcert'] . ';';
        }

        if (isset($params['sslkey'])) {
            $dsn .= 'sslkey=' . $params['sslkey'] . ';';
        }

        if (isset($params['sslcrl'])) {
            $dsn .= 'sslcrl=' . $params['sslcrl'] . ';';
        }

        if (isset($params['application_name'])) {
            $dsn .= 'application_name=' . $params['application_name'] . ';';
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return 'pdo_pgsql';
    }
}
