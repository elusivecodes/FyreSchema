<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\DB\Handlers\Postgres\PostgresConnection;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\Mysql\MysqlSchema;
use Fyre\Schema\Handlers\Postgres\PostgresSchema;
use Fyre\Schema\Handlers\Sqlite\SqliteSchema;
use Fyre\Utility\Traits\MacroTrait;
use WeakMap;

use function array_key_exists;
use function array_shift;
use function class_parents;
use function get_class;
use function ltrim;

/**
 * SchemaRegistry
 */
class SchemaRegistry
{
    use MacroTrait;

    protected array $handlers = [
        MysqlConnection::class => MysqlSchema::class,
        PostgresConnection::class => PostgresSchema::class,
        SqliteConnection::class => SqliteSchema::class,
    ];

    protected WeakMap $schemas;

    /**
     * New SchemaRegistry constructor.
     *
     * @param Container $container The Container.
     */
    public function __construct(
        protected Container $container
    ) {
        $this->schemas = new WeakMap();
    }

    /**
     * Map a Connection class to a Schema handler.
     *
     * @param string $connectionClass The Connection class.
     * @param string $schemaClass The Schema class.
     */
    public function map(string $connectionClass, string $schemaClass): void
    {
        $connectionClass = ltrim($connectionClass, '\\');

        $this->handlers[$connectionClass] = $schemaClass;
    }

    /**
     * Load a shared Schema for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     */
    public function use(Connection $connection): Schema
    {
        return $this->schemas[$connection] ??= $this->build($connection);
    }

    /**
     * Load a Schema for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Schema The Schema.
     *
     * @throws SchemaException if the handler is missing.
     */
    protected function build(Connection $connection): Schema
    {
        $connectionClass = get_class($connection);
        $connectionKey = $connectionClass;

        while (!array_key_exists($connectionKey, $this->handlers)) {
            $classParents ??= class_parents($connection);
            $connectionKey = array_shift($classParents);

            if (!$connectionKey) {
                throw SchemaException::forMissingHandler($connectionClass);
            }
        }

        $schemaClass = $this->handlers[$connectionKey];

        return $this->container->build($schemaClass, ['connection' => $connection]);
    }
}
