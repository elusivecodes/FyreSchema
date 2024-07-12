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
                'Tables.TABLE_SCHEMA' => $this->database,
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

    /**
     * Create a TableSchema.
     *
     * @param string $name The table name.
     * @return TableSchemaInterface The TableSchema.
     */
    protected function tableSchema(string $name): MysqlTableSchema
    {
        return new MysqlTableSchema($this, $name);
    }
}
