<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Postgres;

use Fyre\Schema\Schema;

/**
 * PostgresSchema
 */
class PostgresSchema extends Schema
{
    /**
     * Build a Table.
     *
     * @param string $name The table name.
     * @param array $data The table data.
     * @return PostgresTable The Table.
     */
    protected function buildTable(string $name, array $data): PostgresTable
    {
        return $this->container->build(PostgresTable::class, [
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
    protected function readTables(): array
    {
        $results = $this->connection->select([
            'name' => 'Tables.table_name',
            'comment' => 'OBJ_DESCRIPTION(Classes.oid)',
        ])
            ->from([
                'Tables' => 'information_schema.tables',
            ])
            ->join([
                [
                    'table' => 'pg_catalog.pg_namespace',
                    'alias' => 'Namespaces',
                    'type' => 'INNER',
                    'conditions' => [
                        'Namespaces.nspname = Tables.table_schema',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_class',
                    'alias' => 'Classes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Classes.relnamespace = Namespaces.oid',
                        'Classes.relname = Tables.table_name',
                    ],
                ],
            ])
            ->where([
                'Tables.table_schema' => $this->connection->getConfig()['schema'] ?? 'public',
                'Tables.table_type' => 'BASE TABLE',
            ])
            ->orderBy([
                'Tables.table_name' => 'ASC',
            ])
            ->execute()
            ->all();

        $tables = [];

        foreach ($results as $result) {
            $tableName = $result['name'];

            $tables[$tableName] = [
                'comment' => $result['comment'] ?? '',
            ];
        }

        return $tables;
    }
}
