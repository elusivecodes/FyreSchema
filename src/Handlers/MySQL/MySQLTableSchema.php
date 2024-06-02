<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\MySQL;

use Fyre\Schema\TableSchema;

use const FILTER_VALIDATE_FLOAT;

use function array_map;
use function explode;
use function filter_var;
use function in_array;
use function preg_match;
use function str_ends_with;
use function strtok;
use function substr;

/**
 * MySQLTableSchema
 */
class MySQLTableSchema extends TableSchema
{

    /**
     * Read the table columns data.
     * @return array The table columns data.
     */
    protected function readColumns(): array
    {
        $results = $this->schema->getConnection()
            ->query('SHOW FULL COLUMNS FROM '.$this->tableName)
            ->all();

        $columns = [];

        foreach ($results AS $result) {
            $field = $result['Field'];

            $values = null;
            $length = null;
            $precision = null;
            if (preg_match('/^(decimal|numeric)\(([0-9]+),([0-9]+)\)/', $result['Type'], $match)) {
                $type = $match[1];
                $length = (int) $match[2];
                $precision = (int) $match[3];
            } else if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)\(([0-9]+)\)/', $result['Type'], $match)) {
                $type = $match[1];
                $length = (int) $match[2];
                $precision = 0;
            } else if (preg_match('/^(char|varchar)\(([0-9]+)\)/', $result['Type'], $match)) {
                $type = $match[1];
                $length = (int) $match[2];
            } else if (preg_match('/^(enum|set)\((.*)\)$/', $result['Type'], $match)) {
                $type = $match[1];
                $values = array_map(
                    fn(string $value): string => substr($value, 1, -1),
                    explode(',', $match[2])
                );
            } else {
                $type = $result['Type'];
            }

            $nullable = $result['Null'] === 'YES';
            $default = $result['Default'];

            if ($default === null && $nullable) {
                $default = 'NULL';
            } else if (
                $default &&
                !in_array($default, ['current_timestamp()', 'curdate()', 'curtime()']) &&
                filter_var($default, FILTER_VALIDATE_FLOAT) === false
            ) {
                $default = '\''.$default.'\'';
            }

            $columns[$field] = [
                'type' => $type,
                'length' => $length,
                'precision' => $precision,
                'values' => $values,
                'nullable' => $nullable,
                'unsigned' => str_ends_with($result['Type'], 'unsigned'),
                'default' => $default,
                'charset' => $result['Collation'] !== null ?
                    strtok($result['Collation'], '_') :
                    null,
                'collation' => $result['Collation'],
                'extra' => $result['Extra'],
                'comment' => $result['Comment']
            ];
        }

        return $columns;
    }

    /**
     * Read the table foreign keys data.
     * @return array The table foreign keys data.
     */
    protected function readForeignKeys(): array
    {
        $results = $this->schema->getConnection()
            ->select([
                'KeyColumnUsage.CONSTRAINT_NAME',
                'KeyColumnUsage.COLUMN_NAME',
                'KeyColumnUsage.REFERENCED_TABLE_NAME',
                'KeyColumnUsage.REFERENCED_COLUMN_NAME',
                'ReferentialConstraints.UPDATE_RULE',
                'ReferentialConstraints.DELETE_RULE'
            ])
            ->from([
                'KeyColumnUsage' => 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE'
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                    'alias' => 'ReferentialConstraints',
                    'type' => 'INNER',
                    'conditions' => [
                        'ReferentialConstraints.CONSTRAINT_SCHEMA = KeyColumnUsage.CONSTRAINT_SCHEMA',
                        'ReferentialConstraints.CONSTRAINT_NAME = KeyColumnUsage.CONSTRAINT_NAME',
                        'ReferentialConstraints.TABLE_NAME' => $this->tableName
                    ]
                ]
            ])
            ->where([
                'KeyColumnUsage.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'KeyColumnUsage.TABLE_NAME' => $this->tableName
            ])
            ->orderBy([
                'KeyColumnUsage.ORDINAL_POSITION' => 'ASC'
            ])
            ->execute()
            ->all();

        $foreignKeys = [];

        foreach ($results AS $result) {
            $constraintName = $result['CONSTRAINT_NAME'];

            $foreignKeys[$constraintName] ??= [
                'columns' => [],
                'referencedTable' => $result['REFERENCED_TABLE_NAME'],
                'referencedColumns' => [],
                'update' => $result['UPDATE_RULE'],
                'delete' => $result['DELETE_RULE']
            ];

            $foreignKeys[$constraintName]['columns'][] = $result['COLUMN_NAME'];
            $foreignKeys[$constraintName]['referencedColumns'][] = $result['REFERENCED_COLUMN_NAME'];
        }

        return $foreignKeys;
    }

    /**
     * Read the table indexes data.
     * @return array The table indexes data.
     */
    protected function readIndexes(): array
    {
        $results = $this->schema->getConnection()
            ->query('SHOW INDEXES FROM '.$this->tableName)
            ->all();

        $indexes = [];

        foreach ($results AS $result) {
            $indexName = $result['Key_name'];

            $indexes[$indexName] ??= [
                'columns' => [],
                'unique' => !$result['Non_unique'],
                'type' => $result['Index_type']
            ];

            $indexes[$indexName]['columns'][] = $result['Column_name'];
        }

        return $indexes;
    }

}
