<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\MySQL;

use Fyre\DB\ValueBinder;
use Fyre\Schema\TableSchema;

use function array_map;
use function explode;
use function in_array;
use function preg_match;
use function str_ends_with;
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
            ->select([
                'COLUMN_NAME',
                'DATA_TYPE',
                'CHARACTER_MAXIMUM_LENGTH',
                'NUMERIC_PRECISION',
                'NUMERIC_SCALE',
                'IS_NULLABLE',
                'COLUMN_TYPE',
                'COLUMN_DEFAULT',
                'CHARACTER_SET_NAME',
                'COLLATION_NAME',
                'EXTRA',
                'COLUMN_COMMENT'
            ])
            ->from('INFORMATION_SCHEMA.COLUMNS')
            ->where([
                'TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'TABLE_NAME' => $this->tableName
            ])
            ->orderBy([
                'ORDINAL_POSITION' => 'ASC'
            ])
            ->execute()
            ->all();

        $columns = [];

        foreach ($results AS $result) {
            $columnName = $result['COLUMN_NAME'];

            $values = null;
            $length = null;
            $precision = null;
            if (preg_match('/^(?:decimal|numeric)\(([0-9]+),([0-9]+)\)/', $result['COLUMN_TYPE'], $match)) {
                $length = (int) $match[1];
                $precision = (int) $match[2];
            } else if (preg_match('/^(?:tinyint|smallint|mediumint|int|bigint)\(([0-9]+)\)/', $result['COLUMN_TYPE'], $match)) {
                $length = (int) $match[1];
                $precision = 0;
            } else if (preg_match('/^(?:enum|set)\((.*)\)$/', $result['COLUMN_TYPE'], $match)) {
                $values = array_map(
                    fn(string $value): string => substr($value, 1, -1),
                    explode(',', $match[1])
                );
            } else if (!in_array($result['DATA_TYPE'], ['float', 'double', 'real'])) {
                $length = $result['CHARACTER_MAXIMUM_LENGTH'];
            }

            $columns[$columnName] = [
                'type' => $result['DATA_TYPE'],
                'length' => $length,
                'precision' => $precision,
                'values' => $values,
                'nullable' => $result['IS_NULLABLE'] === 'YES',
                'unsigned' => str_ends_with($result['COLUMN_TYPE'], 'unsigned'),
                'default' => $result['COLUMN_DEFAULT'],
                'charset' => $result['CHARACTER_SET_NAME'],
                'collation' => $result['COLLATION_NAME'],
                'extra' => $result['EXTRA'],
                'comment' => $result['COLUMN_COMMENT']
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
                'KEY_COLUMN_USAGE.CONSTRAINT_NAME',
                'KEY_COLUMN_USAGE.COLUMN_NAME',
                'KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME',
                'KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME',
                'REFERENTIAL_CONSTRAINTS.UPDATE_RULE',
                'REFERENTIAL_CONSTRAINTS.DELETE_RULE'
            ])
            ->from([
                'INFORMATION_SCHEMA.KEY_COLUMN_USAGE'
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                    'type' => 'INNER',
                    'conditions' => [
                        'REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA = KEY_COLUMN_USAGE.CONSTRAINT_SCHEMA',
                        'REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME = KEY_COLUMN_USAGE.CONSTRAINT_NAME',
                        'REFERENTIAL_CONSTRAINTS.TABLE_NAME = KEY_COLUMN_USAGE.TABLE_NAME'
                    ]
                ]
            ])
            ->where([
                'KEY_COLUMN_USAGE.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'KEY_COLUMN_USAGE.TABLE_NAME' => $this->tableName
            ])
            ->orderBy([
                'KEY_COLUMN_USAGE.ORDINAL_POSITION' => 'ASC'
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
        $binder = new ValueBinder();
        $p0 = $binder->bind('PRIMARY');

        $results = $this->schema->getConnection()
            ->select([
                'INDEX_NAME',
                'COLUMN_NAME',
                'NON_UNIQUE',
                'INDEX_TYPE'
            ])
            ->from([
                'INFORMATION_SCHEMA.STATISTICS'
            ])
            ->where([
                'TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'TABLE_NAME' => $this->tableName
            ])
            ->orderBy([
                '(INDEX_NAME = '.$p0.') DESC',
                'NON_UNIQUE' => 'ASC',
                'INDEX_NAME' => 'ASC',
                'SEQ_IN_INDEX' => 'ASC'
            ])
            ->groupBy([
                'INDEX_NAME',
                'COLUMN_NAME'
            ])
            ->execute($binder)
            ->all();

        $indexes = [];

        foreach ($results AS $result) {
            $indexName = $result['INDEX_NAME'];

            $indexes[$indexName] ??= [
                'columns' => [],
                'unique' => !$result['NON_UNIQUE'],
                'type' => $result['INDEX_TYPE']
            ];

            $indexes[$indexName]['columns'][] = $result['COLUMN_NAME'];
        }

        return $indexes;
    }

}
