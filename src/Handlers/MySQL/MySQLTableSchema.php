<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\MySQL;

use
    Fyre\Schema\TableSchemaInterface,
    Fyre\Schema\Traits\TableSchemaTrait;

use function
    array_map,
    count,
    explode,
    preg_match,
    str_ends_with,
    substr;

/**
 * MySQLTableSchema
 */
class MySQLTableSchema implements TableSchemaInterface
{

    use
        TableSchemaTrait;

    /**
     * Read the table columns data.
     * @return array The table columns data.
     */
    protected function readColumns(): array
    {
        $results = $this->schema->getConnection()->builder()
            ->table('INFORMATION_SCHEMA.COLUMNS')
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
            ->where([
                'TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'TABLE_NAME' => $this->tableName
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
            } else {
                $length = $result['CHARACTER_MAXIMUM_LENGTH'] ?? $result['NUMERIC_PRECISION'];
                $precisision = $result['NUMERIC_SCALE'];
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
        $results = $this->schema->getConnection()->builder()
            ->table([
                'KeyColumnUsage' => 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE'
            ])
            ->select([
                'KeyColumnUsage.CONSTRAINT_NAME',
                'KeyColumnUsage.COLUMN_NAME',
                'KeyColumnUsage.REFERENCED_TABLE_NAME',
                'KeyColumnUsage.REFERENCED_COLUMN_NAME',
                'ReferentialConstraints.UPDATE_RULE',
                'ReferentialConstraints.DELETE_RULE'
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS',
                    'alias' => 'ReferentialConstraints',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ReferentialConstraints.CONSTRAINT_SCHEMA = KeyColumnUsage.TABLE_SCHEMA',
                        'ReferentialConstraints.CONSTRAINT_NAME = KeyColumnUsage.CONSTRAINT_NAME'
                    ]
                ]
            ])
            ->where([
                'KeyColumnUsage.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'KeyColumnUsage.TABLE_NAME' => $this->tableName,
                'KeyColumnUsage.REFERENCED_TABLE_SCHEMA IS NOT NULL'
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
        $results = $this->schema->getConnection()->builder()
            ->table([
                'Statistics' => 'INFORMATION_SCHEMA.STATISTICS'
            ])
            ->select([
                'Statistics.INDEX_NAME',
                'Statistics.COLUMN_NAME',
                'Statistics.NON_UNIQUE',
                'Statistics.INDEX_TYPE',
                'KeyColumnUsage.CONSTRAINT_NAME'
            ])
            ->join([
                [
                    'table' => 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE',
                    'alias' => 'KeyColumnUsage',
                    'type' => 'LEFT',
                    'conditions' => [
                        'KeyColumnUsage.TABLE_SCHEMA = Statistics.TABLE_SCHEMA',
                        'KeyColumnUsage.TABLE_NAME = Statistics.TABLE_NAME',
                        'KeyColumnUsage.CONSTRAINT_NAME = Statistics.INDEX_NAME',
                        'KeyColumnUsage.REFERENCED_TABLE_SCHEMA IS NOT NULL'
                    ]
                ]
            ])
            ->where([
                'Statistics.TABLE_SCHEMA' => $this->schema->getDatabaseName(),
                'Statistics.TABLE_NAME' => $this->tableName
            ])
            ->groupBy([
                'Statistics.INDEX_NAME',
                'Statistics.COLUMN_NAME'
            ])
            ->execute()
            ->all();

        $indexes = [];

        foreach ($results AS $result) {
            $indexName = $result['INDEX_NAME'];

            $indexes[$indexName] ??= [
                'columns' => [],
                'unique' => !$result['NON_UNIQUE'],
                'type' => $result['INDEX_TYPE'],
                'foreignKey' => !!$result['CONSTRAINT_NAME']
            ];

            $indexes[$indexName]['columns'][] = $result['COLUMN_NAME'];
        }

        return $indexes;
    }

    /**
     * Get the database type for a column.
     * @param array $column The column data.
     * @return string The database type.
     */
    protected static function getDatabaseType(array $column): string
    {
        $type = $column['type'] ?? null;
        $length = $column['length'] ?? null;

        if ($type === 'tinyint' && $length === 1) {
            return 'boolean';
        }

        return static::$types[$type] ?? 'string';
    }

}
