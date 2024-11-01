<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Cache\Cacher;
use Fyre\DB\Connection;
use Fyre\Schema\Exceptions\SchemaException;

use function array_keys;
use function str_replace;

/**
 * Schema
 */
abstract class Schema
{
    protected Cacher|null $cache;

    protected Connection $connection;

    protected string $database;

    protected array $schemas = [];

    protected array|null $tables = null;

    /**
     * New Schema constructor.
     *
     * @param Connection The Connection.
     * @param Cacher|null The Cacher.
     */
    public function __construct(Connection $connection, Cacher|null $cache = null)
    {
        $this->connection = $connection;
        $this->cache = $cache;
    }

    /**
     * Clear data from the cache.
     *
     * @return Schema The Schema.
     */
    public function clear(): static
    {
        $this->tables = null;

        if ($this->cache) {
            $this->cache->delete($this->getCachePrefix().'.tables');
        }

        return $this;
    }

    /**
     * Get the TableSchema for a table.
     *
     * @param string $name The table name.
     * @return TableSchema The TableSchema.
     *
     * @throws SchemaException if the table is not valid.
     */
    public function describe(string $name): TableSchema
    {
        if (!$this->hasTable($name)) {
            throw SchemaException::forInvalidTable($name);
        }

        return $this->schemas[$name] ??= $this->tableSchema($name);
    }

    /**
     * Get the Cacher.
     *
     * @return Cacher|null The Cacher.
     */
    public function getCache(): Cacher|null
    {
        return $this->cache;
    }

    /**
     * Get the cache prefix.
     *
     * @return string The cache prefix.
     */
    public function getCachePrefix(): string
    {
        $config = $this->connection->getConfig();

        $prefix = $config['cacheKeyPrefix'] ?? $config['database'] ?? '';

        return str_replace(':', '_', $prefix);
    }

    /**
     * Get the Connection.
     *
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the database name.
     *
     * @return string The database name.
     */
    public function getDatabaseName(): string
    {
        return $this->connection->getConfig()['database'] ?? '';
    }

    /**
     * Determine if the schema has a table.
     *
     * @param string $name The table name.
     * @return bool TRUE if the schema has the table, otherwise FALSE.
     */
    public function hasTable(string $name): bool
    {
        return array_key_exists($name, $this->tables());
    }

    /**
     * Get the data for a table.
     *
     * @param string $name The table name.
     * @return array|null The table data.
     */
    public function table(string $name): array|null
    {
        return $this->tables()[$name] ?? null;
    }

    /**
     * Get the names of all schema tables.
     *
     * @return array The names of all schema tables.
     */
    public function tableNames(): array
    {
        return array_keys($this->tables());
    }

    /**
     * Get the data for all schema tables.
     *
     * @return array The schema tables data.
     */
    public function tables(): array
    {
        return $this->tables ??= $this->loadTables();
    }

    /**
     * Load the schema tables data.
     *
     * @return array The schema tables data.
     */
    protected function loadTables(): array
    {
        if (!$this->cache) {
            return $this->readTables();
        }

        return $this->cache->remember(
            $this->getCachePrefix().'.tables',
            [$this, 'readTables'](...)
        );
    }

    /**
     * Read the schema tables data.
     *
     * @return array The schema tables data.
     */
    abstract protected function readTables(): array;

    /**
     * Create a TableSchema.
     *
     * @param string $name The table name.
     * @return TableSchema The TableSchema.
     */
    abstract protected function tableSchema(string $name): TableSchema;
}
