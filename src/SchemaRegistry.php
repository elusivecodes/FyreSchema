<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Cache\Cacher;
use Fyre\DB\Connection;
use Fyre\DB\Handlers\MySQL\MySQLConnection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\MySQL\MySQLSchema;
use WeakMap;

use function array_key_exists;
use function get_class;
use function ltrim;

/**
 * SchemaRegistry
 */
abstract class SchemaRegistry
{

    protected static array $handlers = [
        MySQLConnection::class => MySQLSchema::class
    ];

    protected static WeakMap $schemas;

    protected static Cacher|null $cache = null;

    /**
     * Get the Cache.
     * @return Cacher|null The Cacher.
     */
    public static function getCache(): Cacher|null
    {
        return static::$cache;
    }

    /**
     * Get the Schema for a Connection.
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     */
    public static function getSchema(Connection $connection): Schema
    {
        static::$schemas ??= new WeakMap;

        return static::$schemas[$connection] ??= static::loadSchema($connection);
    }

    /**
     * Set the Cache.
     * @param Cacher|null $cache The Cacher.
     */
    public static function setCache(Cacher|null $cache): void
    {
        static::$cache = $cache;
    }

    /**
     * Set a Schema handler for a Connection class.
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
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     * @throws SchemaException if the handler is missing.
     */
    protected static function loadSchema(Connection $connection): Schema
    {
        $connectionClass = get_class($connection);

        if (!array_key_exists($connectionClass, static::$handlers)) {
            throw SchemaException::forMissingHandler($connectionClass);
        }

        $schemaClass = static::$handlers[$connectionClass];

        return new $schemaClass($connection);
    }

}
