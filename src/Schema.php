<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Cache\CacheManager;
use Fyre\Cache\Cacher;
use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\Schema\Exceptions\SchemaException;

use function array_keys;
use function str_replace;

/**
 * Schema
 */
abstract class Schema
{
    protected const CACHE_KEY = 'schema';

    protected CacheManager $cacheManager;

    protected Connection $connection;

    protected Container $container;

    protected string $database;

    protected array $schemas = [];

    protected array|null $tables = null;

    /**
     * New Schema constructor.
     *
     * @param Container $container The Container.
     * @param CacheManager $cacheManager The CacheManager.
     * @param Connection $connection The Connection.
     */
    public function __construct(Container $container, CacheManager $cacheManager, Connection $connection)
    {
        $this->container = $container;
        $this->cacheManager = $cacheManager;
        $this->connection = $connection;
    }

    /**
     * Clear data from the cache.
     *
     * @return Schema The Schema.
     */
    public function clear(): static
    {
        $this->tables = null;

        $cache = $this->getCache();

        if ($cache) {
            $cache->delete($this->getCachePrefix().'.tables');
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
        return $this->cacheManager->hasConfig(static::CACHE_KEY) ?
            $this->cacheManager->use(static::CACHE_KEY) :
            null;
    }

    /**
     * Get the cache prefix.
     *
     * @return string The cache prefix.
     */
    public function getCachePrefix(): string
    {
        $config = $this->connection->getConfig();

        $prefix = $config['cacheKeyPrefix'] ?? '';
        $prefix = $prefix ? $prefix.'.' : '';

        return str_replace(':', '_', $prefix.$config['database']);
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
     * Determine whether the schema has a table.
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
        $cache = $this->getCache();

        if (!$cache) {
            return $this->readTables();
        }

        return $cache->remember(
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
