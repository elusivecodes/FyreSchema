<?php
declare(strict_types=1);

namespace Fyre\Schema;

use
    Closure,
    Fyre\DB\Connection,
    Fyre\Schema\Exceptions\SchemaException;

use function
    array_keys;

/**
 * Schema
 */
abstract class Schema
{

    protected Connection $connection;

    protected string $database;

    protected array|null $tables = null;

    protected array $schemas = [];

    /**
     * New Schema constructor.
     * @param Connection The Connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $config = $this->connection->getConfig();
        $this->database = $config['database'];
    }

    /**
     * Clear data from the cache.
     */
    public function clear(): static
    {
        $this->tables = null;

        $cache = SchemaRegistry::getCache();

        if ($cache) {
            $cache->forget($this->database.'.tables');
        }

        return $this;
    }

    /**
     * Get the TableSchema for a table.
     * @param string $name The table name.
     * @return TableSchema The TableSchema.
     */
    public function describe(string $name): TableSchema
    {
        if (!$this->hasTable($name)) {
            throw SchemaException::forInvalidTable($name);
        }

        return $this->schemas[$name] ??= $this->tableSchema($name);
    }

    /**
     * Get the Connection.
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the database name.
     * @return string The database name.
     */
    public function getDatabaseName(): string
    {
        return $this->database;
    }

    /**
     * Determine if the schema has a table.
     * @param string $name The table name.
     * @return bool TRUE if the schema has the table, otherwise FALSE.
     */
    public function hasTable(string $name): bool
    {
        return array_key_exists($name, $this->tables());
    }

    /**
     * Get the data for a table.
     * @param string $name The table name.
     * @return array|null The table data.
     */
    public function table(string $name): array|null
    {
        return $this->tables()[$name] ?? null;
    }

    /**
     * Get the names of all schema tables.
     * @return array The names of all schema tables.
     */
    public function tableNames(): array
    {
        return array_keys($this->tables());
    }

    /**
     * Get the data for all schema tables.
     * @return array The schema tables data.
     */
    public function tables(): array
    {
        return $this->tables ??= $this->loadTables();
    }

    /**
     * Load the schema tables data.
     * @return array The schema tables data.
     */
    protected function loadTables(): array
    {
        $cache = SchemaRegistry::getCache();

        if (!$cache) {
            return $this->readTables();
        }

        return $cache->remember(
            $this->database.'.tables',
            Closure::fromCallable([$this, 'readTables'])
        );
    }

    /**
     * Read the schema tables data.
     * @return array The schema tables data.
     */
    abstract protected function readTables(): array;

    /**
     * Create a TableSchema.
     * @param string $name The table name.
     * @return TableSchema The TableSchema.
     */
    abstract protected function tableSchema(string $name): TableSchema;

}
