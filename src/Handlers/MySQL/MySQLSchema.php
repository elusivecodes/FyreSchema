<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\MySQL;

use Fyre\Schema\Schema;

use function strtok;

/**
 * MySQLSchema
 */
class MySQLSchema extends Schema
{

    /**
     * Read the schema tables data.
     * @return array The schema tables data.
     */
    protected function readTables(): array
    {
        $results = $this->connection
            ->query('SHOW TABLE STATUS')
            ->all();

        $tables = [];

        foreach ($results AS $result) {
            $tableName = $result['Name'];

            $tables[$tableName] = [
                'engine' => $result['Engine'],
                'charset' => strtok($result['Collation'], '_'),
                'collation' => $result['Collation'],
                'comment' => $result['Comment']
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
