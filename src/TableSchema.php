<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Closure;
use Fyre\DB\Types\Type;

use function array_key_exists;
use function array_keys;
use function ctype_digit;
use function is_numeric;
use function preg_match;
use function strtolower;

/**
 * TableSchema
 */
abstract class TableSchema
{
    protected static array $types = [];

    protected array|null $columns = null;

    protected array|null $foreignKeys = null;

    protected array|null $indexes = null;

    protected Schema $schema;

    protected string $tableName;

    /**
     * New TableSchema constructor.
     *
     * @param Schema $schema The Schema.
     * @param string $tableName The table name.
     */
    public function __construct(Schema $schema, string $tableName)
    {
        $this->schema = $schema;
        $this->tableName = $tableName;
    }

    /**
     * Clear data from the cache.
     *
     * @return TableSchema The TableSchema.
     */
    public function clear(): static
    {
        $cache = $this->schema->getCache();

        if ($cache) {
            $prefix = $this->schema->getCachePrefix();
            foreach (['columns', 'indexes', 'foreign_keys'] as $key) {
                $cache->delete($prefix.'.'.$key);
            }
        }

        $this->columns = null;
        $this->indexes = null;
        $this->foreignKeys = null;

        return $this;
    }

    /**
     * Get the data for a table column.
     *
     * @param string $name The column name.
     * @return array|null The column data.
     */
    public function column(string $name): array|null
    {
        return $this->columns()[$name] ?? null;
    }

    /**
     * Get the names of all table columns.
     *
     * @return array The names of all table columns.
     */
    public function columnNames(): array
    {
        return array_keys($this->columns());
    }

    /**
     * Get the data for all table columns.
     *
     * @return array The table columns data.
     */
    public function columns(): array
    {
        return $this->columns ??= $this->loadColumns();
    }

    /**
     * Get the default value for a column.
     *
     * @param string $name The column name.
     * @return mixed The default value.
     */
    public function defaultValue(string $name): mixed
    {
        if (!$this->hasColumn($name)) {
            return null;
        }

        $default = $this->column($name)['default'] ?? null;

        if (!$default) {
            return '';
        }

        if (strtolower($default) === 'null') {
            return null;
        }

        if (ctype_digit($default)) {
            return (int) $default;
        }

        if (is_numeric($default)) {
            return (float) $default;
        }

        if (preg_match('/^(["\'])(.*)\1$/', $default, $match)) {
            return $match[2];
        }

        return $this->schema->getConnection()
            ->rawQuery('SELECT '.$default)
            ->fetchColumn();
    }

    /**
     * Get the data for a table foreign key.
     *
     * @param string $name The foreign key name.
     * @return array|null The foreign key data.
     */
    public function foreignKey(string $name): array|null
    {
        return $this->foreignKeys()[$name] ?? null;
    }

    /**
     * Get the data for all table foreign keys.
     *
     * @return array The table foreign keys data.
     */
    public function foreignKeys(): array
    {
        return $this->foreignKeys ??= $this->loadForeignKeys();
    }

    /**
     * Get the Schema.
     *
     * @return Schema The Schema.
     */
    public function getSchema(): Schema
    {
        return $this->schema;
    }

    /**
     * Get the table name.
     *
     * @return string The table name.
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get a Type class for a column.
     *
     * @param string $name The column name.
     * @return Type|null The Type.
     */
    public function getType(string $name): Type|null
    {
        $column = $this->column($name);

        if (!$column) {
            return null;
        }

        $type = static::getDatabaseType($column);

        return $this->schema->getConnection()
            ->getTypeParser()
            ->use($type);
    }

    /**
     * Determine whether the table has an auto increment column.
     *
     * @return bool TRUE if the table has an auto increment column, otherwise FALSE.
     */
    public function hasAutoIncrement(): bool
    {
        foreach ($this->columns() as $column) {
            if (array_key_exists('autoIncrement', $column) && $column['autoIncrement']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the table has a column.
     *
     * @param string $name The column name.
     * @return bool TRUE if the table has the column, otherwise FALSE.
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns());
    }

    /**
     * Determine whether the table has a foreign key.
     *
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool
    {
        return array_key_exists($name, $this->foreignKeys());
    }

    /**
     * Determine whether the table has an index.
     *
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool
    {
        return array_key_exists($name, $this->indexes());
    }

    /**
     * Get the data for a table index.
     *
     * @param string $name The index name.
     * @return array|null The index data.
     */
    public function index(string $name): array|null
    {
        return $this->indexes()[$name] ?? null;
    }

    /**
     * Get the data for all table indexes.
     *
     * @return array The table indexes data.
     */
    public function indexes(): array
    {
        return $this->indexes ??= $this->loadIndexes();
    }

    /**
     * Determine whether a table column is nullable.
     *
     * @param string $name The column name.
     * @return bool TRUE if the column is nullable, otherwise FALSE.
     */
    public function isNullable(string $name): bool
    {
        return $this->columns()[$name]['nullable'] ?? false;
    }

    /**
     * Get the primary key for the table.
     *
     * @return array|null The table primary key.
     */
    public function primaryKey(): array|null
    {
        foreach ($this->indexes() as $index) {
            if ($index['primary']) {
                return $index['columns'];
            }
        }

        return null;
    }

    /**
     * Load the table data.
     *
     * @param string $key The data key.
     * @param Closure $callback The data callback.
     * @return array The table data.
     */
    protected function load(string $key, Closure $callback): array
    {
        $cache = $this->schema->getCache();

        if (!$cache) {
            return $callback();
        }

        return $cache->remember(
            $this->schema->getCachePrefix().'.'.$key,
            $callback
        );
    }

    /**
     * Load the table columns data.
     *
     * @return array The table columns data.
     */
    protected function loadColumns(): array
    {
        return $this->load(
            $this->tableName.'.columns',
            [$this, 'readColumns'](...)
        );
    }

    /**
     * Load the table foreign keys data.
     *
     * @return array The table foreign keys data.
     */
    protected function loadForeignKeys(): array
    {
        return $this->load(
            $this->tableName.'.foreign_keys',
            fn(): array => $this->readForeignKeys()
        );
    }

    /**
     * Load the table indexes data.
     *
     * @return array The table indexes data.
     */
    protected function loadIndexes(): array
    {
        return $this->load(
            $this->tableName.'.indexes',
            fn(): array => $this->readIndexes()
        );
    }

    /**
     * Read the table columns data.
     *
     * @return array The table columns data.
     */
    abstract protected function readColumns(): array;

    /**
     * Read the table foreign keys data.
     *
     * @return array The table foreign keys data.
     */
    abstract protected function readForeignKeys(): array;

    /**
     * Read the table indexes data.
     *
     * @return array The table indexes data.
     */
    abstract protected function readIndexes(): array;

    /**
     * Get the database type for a column.
     *
     * @param array $column The column data.
     * @return string The database type.
     */
    protected static function getDatabaseType(array $column): string
    {
        $type = $column['type'] ?? null;
        $length = $column['length'] ?? null;

        if ($type === 'tinyint' && $length == 1) {
            return 'boolean';
        }

        return static::$types[$type] ?? 'string';
    }
}
