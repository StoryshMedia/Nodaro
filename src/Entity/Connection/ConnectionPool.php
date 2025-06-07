<?php

namespace Smug\Core\Entity\Connection;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Middleware as DriverMiddleware;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Smug\Core\Entity\Connection\Query\QueryBuilder;
use Smug\Core\Entity\Connection\Driver\PDOMySql;
use Smug\Core\Entity\Connection\Driver\PDOPgSql;
use Smug\Core\Entity\Connection\Driver\PDOSqlite;
use Smug\Core\Entity\Connection\Schema\EventListener\SchemaAlterTableListener;
use Smug\Core\Entity\Connection\Schema\EventListener\SchemaColumnDefinitionListener;
use Smug\Core\Entity\Connection\Schema\EventListener\SchemaIndexDefinitionListener;
use Smug\Core\Entity\Connection\Schema\Types\DateTimeType;
use Smug\Core\Entity\Connection\Schema\Types\DateType;
use Smug\Core\Entity\Connection\Schema\Types\EnumType;
use Smug\Core\Entity\Connection\Schema\Types\SetType;
use Smug\Core\Entity\Connection\Schema\Types\TimeType;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;

class ConnectionPool
{
    /**
     * @var string
     */
    public const DEFAULT_CONNECTION_NAME = 'Default';

    /**
     * @var Connection[]
     */
    protected static $connections = [];

    /**
     * @var array<non-empty-string,class-string>
     */
    protected array $customDoctrineTypes = [
        EnumType::TYPE => EnumType::class,
        SetType::TYPE => SetType::class,
    ];

    /**
     * @var array<non-empty-string,class-string>
     */
    protected array $overrideDoctrineTypes = [
        Types::DATE_MUTABLE => DateType::class,
        Types::DATETIME_MUTABLE => DateTimeType::class,
        Types::DATETIME_IMMUTABLE => DateTimeType::class,
        Types::TIME_MUTABLE => TimeType::class,
    ];

    /**
     * @var string[]
     */
    protected static $driverMap = [
        'pdo_mysql' => PDOMySql::class,
        'pdo_sqlite' => PDOSqlite::class,
        'pdo_pgsql' => PDOPgSql::class
    ];

    /**
     * @param string $tableName
     */
    public function getConnectionForTable(string $tableName): Connection
    {
        if (empty($tableName)) {
            throw new \UnexpectedValueException(
                'ConnectionPool->getConnectionForTable() requires a table name to be provided.',
                1459421719
            );
        }

        $connectionName = self::DEFAULT_CONNECTION_NAME;
        if (!empty($_ENV['DATABASE_NAME'])) {
            $connectionName = (string)$_ENV['DATABASE_NAME'];
        }

        return $this->getConnectionByName($connectionName);
    }

    /**
     * @param string $connectionName
     * @throws \Doctrine\DBAL\Exception
     */
    public function getConnectionByName(string $connectionName): Connection
    {
        if (empty($connectionName)) {
            throw new \UnexpectedValueException(
                'ConnectionPool->getConnectionByName() requires a connection name to be provided.',
                1459422125
            );
        }

        if (isset(static::$connections[$connectionName])) {
            return static::$connections[$connectionName];
        }

        $doctrineConfiguration = $this->parseDoctrineConfiguration();

        $connectionParams = $doctrineConfiguration[$connectionName] ?? [];
        if (empty($connectionParams)) {
            $connectionParams = $this->createParamsFromEnv();
        }

        if (empty($connectionParams['wrapperClass'])) {
            $connectionParams['wrapperClass'] = Connection::class;
        }

        if (!is_a($connectionParams['wrapperClass'], Connection::class, true)) {
            throw new \UnexpectedValueException(
                "The 'wrapperClass' for the connection name '" . $connectionName .
                "' needs to be a subclass of '" . Connection::class . "'.",
                1459422968
            );
        }

        if (isset($connectionParams['tableoptions'])) {
            $connectionParams['defaultTableOptions'] = array_replace(
                $connectionParams['defaultTableOptions'] ?? [],
                $connectionParams['tableoptions']
            );
            unset($connectionParams['tableoptions']);
        }

        static::$connections[$connectionName] = $this->getDatabaseConnection($connectionParams);

        return static::$connections[$connectionName];
    }

    /**
     * @internal
     */
    protected function mapCustomDriver(array $connectionParams): array
    {
        if (!isset($connectionParams['driverClass']) && isset(static::$driverMap[$connectionParams['driver']])) {
            $connectionParams['driverClass'] = static::$driverMap[$connectionParams['driver']];
        }

        return $connectionParams;
    }

    protected function getDriverMiddlewares(array $connectionParams): array
    {
        $middlewares = [];

        foreach ($connectionParams['driverMiddlewares'] ?? [] as $className) {
            if (!in_array(DriverMiddleware::class, class_implements($className) ?: [], true)) {
                throw new \UnexpectedValueException('Doctrine Driver Middleware must implement \Doctrine\DBAL\Driver\Middleware', 1677958727);
            }
            $middlewares[] = ServiceGenerationFactory::createInstance($className);
        }

        return $middlewares;
    }

    protected function getDatabaseConnection(array $connectionParams): Connection
    {
        $this->registerDoctrineTypes();

        // Default to UTF-8 connection charset
        if (empty($connectionParams['charset'])) {
            $connectionParams['charset'] = 'utf8';
        }

        $connectionParams = $this->mapCustomDriver($connectionParams);
        $middlewares = $this->getDriverMiddlewares($connectionParams);
        $configuration = $middlewares ? (new Configuration())->setMiddlewares($middlewares) : null;

        /** @var Connection $conn */
        $conn = DriverManager::getConnection($connectionParams, $configuration);
        $conn->prepareConnection($connectionParams['initCommands'] ?? '');

        // Register all custom data types in the type mapping
        foreach ($this->customDoctrineTypes as $type => $className) {
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping($type, $type);
        }

        // Register all override data types in the type mapping
        foreach ($this->overrideDoctrineTypes as $type => $className) {
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping($type, $type);
        }

        // Handler for building custom data type column definitions
        // in the SchemaManager
        $conn->getDatabasePlatform()->getEventManager()->addEventListener(
            Events::onSchemaColumnDefinition,
            ServiceGenerationFactory::createInstance(SchemaColumnDefinitionListener::class)
        );

        // Handler for enhanced index definitions in the SchemaManager
        $conn->getDatabasePlatform()->getEventManager()->addEventListener(
            Events::onSchemaIndexDefinition,
            ServiceGenerationFactory::createInstance(SchemaIndexDefinitionListener::class)
        );

        // Handler for adding custom database platform options to ALTER TABLE
        // requests in the SchemaManager
        $conn->getDatabasePlatform()->getEventManager()->addEventListener(
            Events::onSchemaAlterTable,
            ServiceGenerationFactory::createInstance(SchemaAlterTableListener::class)
        );

        return $conn;
    }

    public function getQueryBuilderForTable(string $tableName): QueryBuilder
    {
        if (empty($tableName)) {
            throw new \UnexpectedValueException(
                'ConnectionPool->getQueryBuilderForTable() requires a connection name to be provided.',
                1459423448
            );
        }

        return $this->getConnectionForTable($tableName)->createQueryBuilder();
    }

    /**
     * @internal
     */
    public function getConnectionNames(): array
    {
        return array_keys($this->parseDoctrineConfiguration()['dbal'] ?? []);
    }

    /**
     * @internal
     */
    public function registerDoctrineTypes(): void
    {
        // Register custom data types
        foreach ($this->customDoctrineTypes as $type => $className) {
            if (!Type::hasType($type)) {
                Type::addType($type, $className);
            }
        }
        // Override data types
        foreach ($this->overrideDoctrineTypes as $type => $className) {
            if (!Type::hasType($type)) {
                Type::addType($type, $className);
                continue;
            }
            Type::overrideType($type, $className);
        }
    }

    public function resetConnections(): void
    {
        static::$connections = [];
    }

    public function createParamsFromEnv(): array
    {
        return [
            "driver" => "pdo_mysql",
            "server_version" => "10.6.11",
            "charset" => "utf8mb4",
            "default_table_options" => [
                "charset" => "utf8mb4",
                "collate" => "utf8mb4_unicode_ci"
            ],
            "url" => $_ENV['DATABASE_URL'] ?? '',
            "types" => [
                "uuid" => "Ramsey\Uuid\Doctrine\UuidType",
                "stringfield" => "Smug\Core\Entity\Base\Field\StringField",
                "slugField" => "Smug\Core\Entity\Base\Field\SlugField",
                "jsonField" => "Smug\Core\Entity\Base\Field\JsonField"
            ]
        ];
    }

    private function parseDoctrineConfiguration(): array
    {
        if (!DataHandler::doesFileExist(dirname(__DIR__) . '/../../config/packages/doctrine.yaml')) {
            return [];
        }

        return DataHandler::getYamlFile(dirname(__DIR__) . '/../../config/packages/doctrine.yaml');
    }
}
