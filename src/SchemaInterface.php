<?php
declare(strict_types=1);

namespace Fyre\Schema;

use
    Fyre\DB\Connection;

/**
 * SchemaInterface
 */
interface SchemaInterface
{

    /**
     * New Schema constructor.
     * @param Connection The Connection.
     */
    public function __construct(Connection $connection);

    /**
     * Clear data from the cache.
     * @return Schema The Schema.
     */
    public function clear(): static;

    /**
     * Get the TableSchema for a table.
     * @param string $name The table name.
     * @return TableSchemaInterface The TableSchema.
     */
    public function describe(string $name): TableSchemaInterface;

    /**
     * Get the cache prefix.
     * @return string The cache prefix.
     */
    public function getCachePrefix(): string;

    /**
     * Get the Connection.
     * @return Connection The Connection.
     */
    public function getConnection(): Connection;

    /**
     * Get the database name.
     * @return string The database name.
     */
    public function getDatabaseName(): string;

    /**
     * Determine if the schema has a table.
     * @param string $name The table name.
     * @return bool TRUE if the schema has the table, otherwise FALSE.
     */
    public function hasTable(string $name): bool;

    /**
     * Get the data for a table.
     * @param string $name The table name.
     * @return array|null The table data.
     */
    public function table(string $name): array|null;

    /**
     * Get the names of all schema tables.
     * @return array The names of all schema tables.
     */
    public function tableNames(): array;

    /**
     * Get the data for all schema tables.
     * @return array The schema tables data.
     */
    public function tables(): array;

}
