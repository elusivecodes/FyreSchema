<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Cache\Cacher;
use Fyre\DB\Connection;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\DB\Handlers\Postgres\PostgresConnection;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\Mysql\MysqlSchema;
use Fyre\Schema\Handlers\Postgres\PostgresSchema;
use Fyre\Schema\Handlers\Sqlite\SqliteSchema;
use WeakMap;

use function array_key_exists;
use function array_shift;
use function class_parents;
use function get_class;
use function ltrim;

/**
 * SchemaRegistry
 */
abstract class SchemaRegistry
{
    protected static Cacher|null $cache = null;

    protected static array $handlers = [
        MysqlConnection::class => MysqlSchema::class,
        PostgresConnection::class => PostgresSchema::class,
        SqliteConnection::class => SqliteSchema::class,
    ];

    protected static WeakMap $schemas;

    /**
     * Get the Cache.
     *
     * @return Cacher|null The Cacher.
     */
    public static function getCache(): Cacher|null
    {
        return static::$cache;
    }

    /**
     * Get the Schema for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     */
    public static function getSchema(Connection $connection): Schema
    {
        static::$schemas ??= new WeakMap();

        return static::$schemas[$connection] ??= static::loadSchema($connection);
    }

    /**
     * Set the Cache.
     *
     * @param Cacher|null $cache The Cacher.
     */
    public static function setCache(Cacher|null $cache): void
    {
        static::$cache = $cache;
    }

    /**
     * Set a Schema handler for a Connection class.
     *
     * @param string $connectionClass The Connection class.
     * @param string $schemaClass The Schema class.
     */
    public static function setHandler(string $connectionClass, string $schemaClass): void
    {
        $connectionClass = ltrim($connectionClass, '\\');

        static::$handlers[$connectionClass] = $schemaClass;
    }

    /**
     * Load a Schema for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     *
     * @throws SchemaException if the handler is missing.
     */
    protected static function loadSchema(Connection $connection): Schema
    {
        $connectionClass = get_class($connection);
        $connectionKey = $connectionClass;

        while (!array_key_exists($connectionKey, static::$handlers)) {
            $classParents ??= class_parents($connection);
            $connectionKey = array_shift($classParents);

            if (!$connectionKey) {
                throw SchemaException::forMissingHandler($connectionClass);
            }
        }

        $schemaClass = static::$handlers[$connectionKey];

        return new $schemaClass($connection);
    }
}
