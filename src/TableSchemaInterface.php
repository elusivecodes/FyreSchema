<?php
declare(strict_types=1);

namespace Fyre\Schema;

use
    Fyre\DB\Types\Type;

/**
 * TableSchemaInterface
 */
interface TableSchemaInterface
{

    /**
     * New TableSchema constructor.
     * @param SchemaInterface $schema The Schema.
     * @param string $tableName The table name.
     */
    public function __construct(SchemaInterface $schema, string $tableName);

    /**
     * Clear data from the cache.
     * @return TableSchemaInterface The TableSchema.
     */
    public function clear(): static;

    /**
     * Get the data for a table column.
     * @param string $name The column name.
     * @return array|null The column data.
     */
    public function column(string $name): array|null;

    /**
     * Get the names of all table columns.
     * @return array The names of all table columns.
     */
    public function columnNames(): array;

    /**
     * Get the data for all table columns.
     * @return array The table columns data.
     */
    public function columns(): array;

    /**
     * Get the default value for a column.
     * @param string $name The column name.
     * @return mixed The default value.
     */
    public function defaultValue(string $name): mixed;

    /**
     * Get the data for a table foreign key.
     * @param string $name The foreign key name.
     * @return array|null The foreign key data.
     */
    public function foreignKey(string $name): array|null;

    /**
     * Get the data for all table foreign keys.
     * @return array The table foreign keys data.
     */
    public function foreignKeys();

    /**
     * Get the Schema.
     * @return SchemaInterface The Schema.
     */
    public function getSchema(): SchemaInterface;

    /**
     * Get the table name.
     * @return string The table name.
     */
    public function getTableName(): string;

    /**
     * Get a Type class for a column.
     * @param string $name The column name.
     * @return Type|null The Type.
     */
    public function getType(string $name): Type|null;

    /**
     * Get the data for a table index.
     * @param string $name The index name.
     * @return array|null The index data.
     */
    public function index(string $name): array|null;

    /**
     * Get the data for all table indexes.
     * @return array The table indexes data.
     */
    public function indexes(): array;

    /**
     * Determine if the table has a column.
     * @param string $name The column name.
     * @return bool TRUE if the table has the column, otherwise FALSE.
     */
    public function hasColumn(string $name): bool;

    /**
     * Determine if the table has a foreign key.
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool;

    /**
     * Determine if the table has an index.
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool;

    /**
     * Determine if a table column is nullable.
     * @param string $name The column name.
     * @return bool TRUE if the column is nullable, otherwise FALSE.
     */
    public function isNullable(string $name): bool;

    /**
     * Get the primary key for the table.
     * @return array|null The table primary key.
     */
    public function primaryKey(): array|null;

}
