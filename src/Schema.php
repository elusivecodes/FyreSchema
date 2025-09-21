<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Closure;
use Fyre\Cache\CacheManager;
use Fyre\Cache\Cacher;
use Fyre\Collection\Collection;
use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Utility\Traits\MacroTrait;
use Generator;

use function array_keys;
use function str_replace;

/**
 * Schema
 */
abstract class Schema
{
    use MacroTrait;

    protected const CACHE_KEY = 'schema';

    protected static string $tableClass = Table::class;

    protected string $database;

    protected array $loadedTables = [];

    protected array $schemas = [];

    protected array|null $tables = null;

    /**
     * New Schema constructor.
     *
     * @param Container $container The Container.
     * @param CacheManager $cacheManager The CacheManager.
     * @param Connection $connection The Connection.
     */
    public function __construct(
        protected Container $container,
        protected CacheManager $cacheManager,
        protected Connection $connection
    ) {}

    /**
     * Clear the table data (including cache).
     *
     * @return Schema The Schema.
     */
    public function clear(): static
    {
        $this->tables = null;
        $this->loadedTables = [];

        $cache = $this->getCache();

        if ($cache) {
            $cache->delete($this->getCachePrefix().'.tables');
        }

        return $this;
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
        $this->loadTables();

        return array_key_exists($name, $this->tables);
    }

    /**
     * Load data via a callback using the cache.
     *
     * @param string $key The data key.
     * @param Closure $callback The data callback.
     * @return array The data.
     */
    public function load(string $key, Closure $callback): array
    {
        $cache = $this->getCache();

        if (!$cache) {
            return $callback();
        }

        return $cache->remember(
            $this->getCachePrefix().'.'.$key,
            $callback
        );
    }

    /**
     * Load a Table.
     *
     * @param string $name The table name.
     * @return Table The Table.
     */
    public function table(string $name): Table
    {
        $this->loadTables();

        if (!array_key_exists($name, $this->tables)) {
            throw SchemaException::forInvalidTable($name);
        }

        return $this->loadedTables[$name] ??= $this->buildTable($name, $this->tables[$name]);
    }

    /**
     * Get the names of all schema tables.
     *
     * @return array The names of all schema tables.
     */
    public function tableNames(): array
    {
        $this->loadTables();

        return array_keys($this->tables);
    }

    /**
     * Get all schema tables.
     *
     * @return Collection The schema tables.
     */
    public function tables(): Collection
    {
        $this->loadTables();

        return new Collection(
            function(): Generator {
                foreach ($this->tables as $name => $data) {
                    yield $name => $this->loadedTables[$name] ??= $this->buildTable($name, $data);
                }
            }
        );
    }

    /**
     * Build a Table.
     *
     * @param string $name The table name.
     * @param array $data The table data.
     * @return Table The Table.
     */
    abstract protected function buildTable(string $name, array $data): Table;

    /**
     * Load the schema tables data.
     */
    protected function loadTables(): void
    {
        $this->tables ??= $this->load(
            'tables',
            [$this, 'readTables'](...)
        );
    }

    /**
     * Read the schema tables data.
     *
     * @return array The schema tables data.
     */
    abstract protected function readTables(): array;
}
