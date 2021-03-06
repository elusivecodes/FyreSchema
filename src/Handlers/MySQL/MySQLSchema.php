<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\MySQL;

use
    Fyre\Schema\SchemaInterface,
    Fyre\Schema\Traits\SchemaTrait;

/**
 * MySQLSchema
 */
class MySQLSchema implements SchemaInterface
{

    use
        SchemaTrait;

    /**
     * Read the schema tables data.
     * @return array The schema tables data.
     */
    protected function readTables(): array
    {
        $results = $this->connection->builder()
            ->table([
                'Tables' => 'INFORMATION_SCHEMA.TABLES'
            ])
            ->select([
                'Tables.TABLE_NAME',
                'Tables.ENGINE',
                'CollationCharacterSetApplicability.CHARACTER_SET_NAME',
                'Tables.TABLE_COLLATION',
                'Tables.TABLE_COMMENT'
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.COLLATION_CHARACTER_SET_APPLICABILITY',
                    'alias' => 'CollationCharacterSetApplicability',
                    'type' => 'LEFT',
                    'conditions' => [
                        'CollationCharacterSetApplicability.COLLATION_NAME = Tables.TABLE_COLLATION'
                    ]
                ]
            ])
            ->where([
                'Tables.TABLE_SCHEMA' => $this->database
            ])
            ->execute()
            ->all();

        $tables = [];

        foreach ($results AS $result) {
            $tableName = $result['TABLE_NAME'];

            $tables[$tableName] = [
                'engine' => $result['ENGINE'],
                'charset' => $result['CHARACTER_SET_NAME'],
                'collation' => $result['TABLE_COLLATION'],
                'comment' => $result['TABLE_COMMENT']
            ];
        }

        return $tables;
    }

    /**
     * Create a TableSchema.
     * @param string $name The table name.
     * @return TableSchemaInterface The TableSchema.
     */
    protected function tableSchema(string $name): MySQLTableSchema
    {
        return new MySQLTableSchema($this, $name);
    }

}
