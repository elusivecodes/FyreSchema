<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Mysql;

use Fyre\DB\ValueBinder;
use Fyre\Schema\TableSchema;

use function array_map;
use function explode;
use function in_array;
use function preg_match;
use function str_ends_with;
use function strtolower;
use function substr;

/**
 * MysqlTableSchema
 */
class MysqlTableSchema extends TableSchema
{
    protected static array $types = [
        'bigint' => 'integer',
        'boolean' => 'boolean',
        'date' => 'date',
        'datetime' => 'datetime',
        'decimal' => 'decimal',
        'double' => 'float',
        'float' => 'float',
        'int' => 'integer',
        'json' => 'json',
        'smallint' => 'integer',
        'time' => 'time',
        'timestamp' => 'datetime',
        'tinyint' => 'integer',
    ];

    /**
     * Read the table columns data.
     *
     * @return array The table columns data.
     */
    protected function readColumns(): array
    {
        $results = $this->schema->getConnection()
            ->select([
                'name' => 'Columns.COLUMN_NAME',
                'type' => 'Columns.DATA_TYPE',
                'char_length' => 'Columns.CHARACTER_MAXIMUM_LENGTH',
                'nullable' => 'Columns.IS_NULLABLE',
                'col_type' => 'Columns.COLUMN_TYPE',
                'col_default' => 'Columns.COLUMN_DEFAULT',
                'charset' => 'Columns.CHARACTER_SET_NAME',
                'collation' => 'Columns.COLLATION_NAME',
                'extra' => 'Columns.EXTRA',
                'comment' => 'Columns.COLUMN_COMMENT',
            ])
            ->from([
                'Columns' => 'INFORMATION_SCHEMA.COLUMNS',
            ])
            ->where([
                'Columns.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'Columns.TABLE_NAME' => $this->tableName,
            ])
            ->orderBy([
                'Columns.ORDINAL_POSITION' => 'ASC',
            ])
            ->execute()
            ->all();

        $columns = [];

        foreach ($results as $result) {
            $columnName = $result['name'];

            $values = null;
            $length = null;
            $precision = null;
            if (preg_match('/^(?:decimal|numeric)\(([0-9]+),([0-9]+)\)/', $result['col_type'], $match)) {
                $length = (int) $match[1];
                $precision = (int) $match[2];
            } else if (preg_match('/^(?:tinyint|smallint|mediumint|int|bigint|bit)\(([0-9]+)\)/', $result['col_type'], $match)) {
                $length = (int) $match[1];
                $precision = 0;
            } else if (preg_match('/^(?:datetime|time|timestamp)\(([0-9]+)\)/', $result['col_type'], $match)) {
                $precision = (int) $match[1];
            } else if (preg_match('/^(?:enum|set)\((.*)\)$/', $result['col_type'], $match)) {
                $values = array_map(
                    fn(string $value): string => substr($value, 1, -1),
                    explode(',', $match[1])
                );
            } else if (!in_array($result['type'], ['float', 'double', 'real'])) {
                $length = $result['char_length'];
            }

            $nullable = $result['nullable'] === 'YES';
            $default = $result['col_default'];

            if ($nullable) {
                $default ??= 'NULL';
            }

            $columns[$columnName] = [
                'type' => $result['type'],
                'length' => $length,
                'precision' => $precision,
                'values' => $values,
                'nullable' => $nullable,
                'unsigned' => str_ends_with($result['col_type'], 'unsigned'),
                'default' => $default,
                'charset' => $result['charset'],
                'collation' => $result['collation'],
                'comment' => $result['comment'],
                'autoIncrement' => $result['extra'] === 'auto_increment',
            ];
        }

        return $columns;
    }

    /**
     * Read the table foreign keys data.
     *
     * @return array The table foreign keys data.
     */
    protected function readForeignKeys(): array
    {
        $results = $this->schema->getConnection()
            ->select([
                'name' => 'KeyColumns.CONSTRAINT_NAME',
                'column_name' => 'KeyColumns.COLUMN_NAME',
                'ref_table_name' => 'KeyColumns.REFERENCED_TABLE_NAME',
                'ref_column' => 'KeyColumns.REFERENCED_COLUMN_NAME',
                'on_update' => 'ReferentialConstraints.UPDATE_RULE',
                'on_delete' => 'ReferentialConstraints.DELETE_RULE',
            ])
            ->from([
                'KeyColumns' => 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE',
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                    'alias' => 'ReferentialConstraints',
                    'type' => 'INNER',
                    'conditions' => [
                        'ReferentialConstraints.CONSTRAINT_SCHEMA = KeyColumns.CONSTRAINT_SCHEMA',
                        'ReferentialConstraints.CONSTRAINT_NAME = KeyColumns.CONSTRAINT_NAME',
                        'ReferentialConstraints.TABLE_NAME = KeyColumns.TABLE_NAME',
                    ],
                ],
            ])
            ->where([
                'KeyColumns.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'KeyColumns.TABLE_NAME' => $this->tableName,
            ])
            ->orderBy([
                'KeyColumns.ORDINAL_POSITION' => 'ASC',
            ])
            ->execute()
            ->all();

        $foreignKeys = [];

        foreach ($results as $result) {
            $constraintName = $result['name'];

            $foreignKeys[$constraintName] ??= [
                'columns' => [],
                'referencedTable' => $result['ref_table_name'],
                'referencedColumns' => [],
                'update' => $result['on_update'],
                'delete' => $result['on_delete'],
            ];

            $foreignKeys[$constraintName]['columns'][] = $result['column_name'];
            $foreignKeys[$constraintName]['referencedColumns'][] = $result['ref_column'];
        }

        return $foreignKeys;
    }

    /**
     * Read the table indexes data.
     *
     * @return array The table indexes data.
     */
    protected function readIndexes(): array
    {
        $binder = new ValueBinder();
        $p0 = $binder->bind('PRIMARY');

        $results = $this->schema->getConnection()
            ->select([
                'name' => 'Statistics.INDEX_NAME',
                'column_name' => 'Statistics.COLUMN_NAME',
                'not_unique' => 'Statistics.NON_UNIQUE',
                'type' => 'Statistics.INDEX_TYPE',
            ])
            ->from([
                'Statistics' => 'INFORMATION_SCHEMA.STATISTICS',
            ])
            ->where([
                'Statistics.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'Statistics.TABLE_NAME' => $this->tableName,
            ])
            ->groupBy([
                'Statistics.INDEX_NAME',
                'Statistics.COLUMN_NAME',
            ])
            ->orderBy([
                '(Statistics.INDEX_NAME = '.$p0.') DESC',
                'Statistics.NON_UNIQUE' => 'ASC',
                'Statistics.INDEX_NAME' => 'ASC',
                'Statistics.SEQ_IN_INDEX' => 'ASC',
            ])
            ->execute($binder)
            ->all();

        $indexes = [];

        foreach ($results as $result) {
            $indexName = $result['name'];

            $indexes[$indexName] ??= [
                'columns' => [],
                'unique' => !$result['not_unique'],
                'primary' => $indexName === 'PRIMARY',
                'type' => strtolower($result['type']),
            ];

            $indexes[$indexName]['columns'][] = $result['column_name'];
        }

        return $indexes;
    }
}
