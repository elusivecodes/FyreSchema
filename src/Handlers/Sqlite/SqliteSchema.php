<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\Schema\Schema;
use Override;

/**
 * SqliteSchema
 */
class SqliteSchema extends Schema
{
    /**
     * Build a Table.
     *
     * @param string $name The table name.
     * @param array $data The table data.
     * @return SqliteTable The Table.
     */
    #[Override]
    protected function buildTable(string $name, array $data): SqliteTable
    {
        return $this->container->build(SqliteTable::class, [
            'schema' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Read the schema tables data.
     *
     * @return array The schema tables data.
     */
    #[Override]
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
}
