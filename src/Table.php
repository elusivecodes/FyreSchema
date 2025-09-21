<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Collection\Collection;
use Fyre\Container\Container;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Utility\Traits\MacroTrait;
use Generator;

use function array_key_exists;
use function array_keys;

/**
 * Table
 */
abstract class Table
{
    use MacroTrait;

    protected array|null $columns = null;

    protected array|null $foreignKeys = null;

    protected array|null $indexes = null;

    protected array $loadedColumns = [];

    protected array $loadedForeignKeys = [];

    protected array $loadedIndexes = [];

    /**
     * New Table constructor.
     *
     * @param Container $container The Container.
     * @param Schema $schema The Schema.
     * @param string $name The table name.
     * @param string|null $comment The table comment.
     */
    public function __construct(
        protected Container $container,
        protected Schema $schema,
        protected string $name,
        protected string|null $comment = null,
    ) {}

    /**
     * Clear the table data (including cache).
     *
     * @return Table The Table.
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
        $this->loadedColumns = [];
        $this->loadedIndexes = [];
        $this->loadedForeignKeys = [];

        return $this;
    }

    /**
     * Get a table Column.
     *
     * @param string $name The column name.
     * @return Column The Column.
     */
    public function column(string $name): Column
    {
        $this->loadColumns();

        if (!array_key_exists($name, $this->columns)) {
            throw SchemaException::forInvalidColumn($this->name, $name);
        }

        return $this->loadedColumns[$name] ??= $this->buildColumn($name, $this->columns[$name]);
    }

    /**
     * Get the names of all table columns.
     *
     * @return array The names of all table columns.
     */
    public function columnNames(): array
    {
        $this->loadColumns();

        return array_keys($this->columns);
    }

    /**
     * Get all table columns.
     *
     * @return Collection The table columns.
     */
    public function columns(): Collection
    {
        $this->loadColumns();

        return new Collection(
            function(): Generator {
                foreach ($this->columns as $name => $data) {
                    yield $name => $this->loadedColumns[$name] ??= $this->buildColumn($name, $data);
                }
            }
        );
    }

    /**
     * Get a table foreign key.
     *
     * @param string $name The foreign key name.
     * @return ForeignKey The ForeignKey.
     */
    public function foreignKey(string $name): ForeignKey
    {
        $this->loadForeignKeys();

        if (!array_key_exists($name, $this->foreignKeys)) {
            throw SchemaException::forInvalidForeignKey($this->name, $name);
        }

        return $this->loadedForeignKeys[$name] ??= $this->buildForeignKey($name, $this->foreignKeys[$name]);
    }

    /**
     * Get the data for all table foreign keys.
     *
     * @return Collection The table foreign keys data.
     */
    public function foreignKeys(): Collection
    {
        $this->loadForeignKeys();

        return new Collection(
            function(): Generator {
                foreach ($this->foreignKeys as $name => $data) {
                    yield $name => $this->loadedForeignKeys[$name] ??= $this->buildForeignKey($name, $data);
                }
            }
        );
    }

    /**
     * Get the table comment.
     *
     * @return string|null The table comment.
     */
    public function getComment(): string|null
    {
        return $this->comment;
    }

    /**
     * Get the table name.
     *
     * @return string The table name.
     */
    public function getName(): string
    {
        return $this->name;
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
     * Determine whether the table has an auto increment column.
     *
     * @return bool TRUE if the table has an auto increment column, otherwise FALSE.
     */
    public function hasAutoIncrement(): bool
    {
        $this->loadColumns();

        foreach ($this->columns as $data) {
            if ($data['autoIncrement'] ?? false) {
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
        $this->loadColumns();

        return array_key_exists($name, $this->columns);
    }

    /**
     * Determine whether the table has a foreign key.
     *
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool
    {
        $this->loadForeignKeys();

        return array_key_exists($name, $this->foreignKeys);
    }

    /**
     * Determine whether the table has an index.
     *
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool
    {
        $this->loadIndexes();

        return array_key_exists($name, $this->indexes);
    }

    /**
     * Get a table index.
     *
     * @param string $name The index name.
     * @return Index The Index.
     */
    public function index(string $name): Index
    {
        $this->loadIndexes();

        if (!array_key_exists($name, $this->indexes)) {
            throw SchemaException::forInvalidIndex($this->name, $name);
        }

        return $this->loadedIndexes[$name] ??= $this->buildIndex($name, $this->indexes[$name]);
    }

    /**
     * Get the data for all table indexes.
     *
     * @return Collection The table indexes data.
     */
    public function indexes(): Collection
    {
        $this->loadIndexes();

        return new Collection(
            function(): Generator {
                foreach ($this->indexes as $name => $data) {
                    yield $name => $this->loadedIndexes[$name] ??= $this->buildIndex($name, $data);
                }
            }
        );
    }

    /**
     * Get the primary key for the table.
     *
     * @return array|null The table primary key.
     */
    public function primaryKey(): array|null
    {
        $this->loadIndexes();

        foreach ($this->indexes as $data) {
            if ($data['primary'] ?? false) {
                return $data['columns'] ?? null;
            }
        }

        return null;
    }

    /**
     * Get the table data as an array.
     *
     * @return array The table data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'comment' => $this->comment,
        ];
    }

    /**
     * Build a Column.
     *
     * @param string $name The column name.
     * @param array $data The column data.
     * @return Column The Column.
     */
    abstract protected function buildColumn(string $name, array $data): Column;

    /**
     * Build a ForeignKey.
     *
     * @param string $name The foreign key name.
     * @param array $data The foreign key data.
     * @return ForeignKey The ForeignKey.
     */
    protected function buildForeignKey(string $name, array $data): ForeignKey
    {
        return $this->container->build(ForeignKey::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Build an Index.
     *
     * @param string $name The index key name.
     * @param array $data The index key data.
     * @return Index The Index.
     */
    protected function buildIndex(string $name, array $data): Index
    {
        return $this->container->build(Index::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Load the table columns data.
     */
    protected function loadColumns(): void
    {
        $this->columns ??= $this->schema->load(
            $this->name.'.columns',
            [$this, 'readColumns'](...)
        );
    }

    /**
     * Load the table foreign keys data.
     */
    protected function loadForeignKeys(): void
    {
        $this->foreignKeys ??= $this->schema->load(
            $this->name.'.foreign_keys',
            [$this, 'readForeignKeys'](...)
        );
    }

    /**
     * Load the table indexes data.
     */
    protected function loadIndexes(): void
    {
        $this->indexes ??= $this->schema->load(
            $this->name.'.indexes',
            [$this, 'readIndexes'](...)
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
}
