<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\Schema\Schema;

/**
 * SqliteSchema
 */
class SqliteSchema extends Schema
{
    /**
     * Read the schema tables data.
     *
     * @return array The schema tables data.
     */
    protected function readTables(): array
    {
        $results = $this->connection->select([
            'Master.name',
        ])
            ->from([
                'Master' => 'sqlite_master ',
            ])
            ->where([
                'Master.type' => 'table',
                'Master.name !=' => 'sqlite_sequence',
            ])
            ->orderBy([
                'Master.name' => 'ASC',
            ])
            ->execute()
            ->all();

        $tables = [];

        foreach ($results as $result) {
            $tableName = $result['name'];

            $tables[$tableName] = [];
        }

        return $tables;
    }

    /**
     * Create a TableSchema.
     *
     * @param string $name The table name.
     * @return TableSchemaInterface The TableSchema.
     */
    protected function tableSchema(string $name): SqliteTableSchema
    {
        return $this->container->build(SqliteTableSchema::class, ['schema' => $this, 'tableName' => $name]);
    }
}
