<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Mysql;

use Fyre\Schema\Schema;

/**
 * MysqlSchema
 */
class MysqlSchema extends Schema
{
    /**
     * Build a Table.
     *
     * @param string $name The table name.
     * @param array $data The table data.
     * @return MysqlTable The Table.
     */
    protected function buildTable(string $name, array $data): MysqlTable
    {
        return $this->container->build(MysqlTable::class, [
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
            'name' => 'Tables.TABLE_NAME',
            'engine' => 'Tables.ENGINE',
            'charset' => 'CollationCharacterSetApplicability.CHARACTER_SET_NAME',
            'collation' => 'Tables.TABLE_COLLATION',
            'comment' => 'Tables.TABLE_COMMENT',
        ])
            ->from([
                'Tables' => 'INFORMATION_SCHEMA.TABLES',
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.COLLATION_CHARACTER_SET_APPLICABILITY',
                    'alias' => 'CollationCharacterSetApplicability',
                    'type' => 'INNER',
                    'conditions' => [
                        'CollationCharacterSetApplicability.COLLATION_NAME = Tables.TABLE_COLLATION',
                    ],
                ],
            ])
            ->where([
                'Tables.TABLE_SCHEMA' => $this->getDatabaseName(),
                'Tables.TABLE_TYPE' => 'BASE TABLE',
            ])
            ->orderBy([
                'Tables.TABLE_NAME' => 'ASC',
            ])
            ->execute()
            ->all();

        $tables = [];

        foreach ($results as $result) {
            $tableName = $result['name'];

            $tables[$tableName] = [
                'engine' => $result['engine'],
                'charset' => $result['charset'],
                'collation' => $result['collation'],
                'comment' => $result['comment'],
            ];
        }

        return $tables;
    }
}
