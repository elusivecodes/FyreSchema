<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\DB\ValueBinder;
use Fyre\Schema\TableSchema;

use function array_column;
use function count;
use function implode;
use function preg_match;
use function strtolower;

/**
 * SqliteTableSchema
 */
class SqliteTableSchema extends TableSchema
{
    protected static array $types = [
        'bigint' => 'integer',
        'boolean' => 'boolean',
        'date' => 'date',
        'datetime' => 'datetime',
        'datetimefractional' => 'datetime-fractional',
        'decimal' => 'decimal',
        'double' => 'float',
        'float' => 'float',
        'int' => 'integer',
        'integer' => 'integer',
        'json' => 'json',
        'numeric' => 'decimal',
        'real' => 'float',
        'smallint' => 'integer',
        'time' => 'time',
        'timestamp' => 'datetime',
        'timestampfractional' => 'datetime-fractional',
        'timestamptimezone' => 'datetime-timezone',
        'tinyint' => 'integer',
    ];

    /**
     * Read the table columns data.
     *
     * @return array The table columns data.
     */
    protected function readColumns(): array
    {
        $binder = new ValueBinder();
        $p0 = $binder->bind($this->tableName);

        $connection = $this->schema->getConnection();

        $results = $connection->select([
            'Columns.name',
            'Columns.type',
            'not_null' => 'Columns."notnull"',
            'col_default' => 'Columns.dflt_value',
            'Columns.pk',
        ])
            ->from([
                'Columns' => 'PRAGMA_TABLE_INFO('.$p0.')',
            ])
            ->execute($binder)
            ->all();

        $columns = [];
        $primaryKeys = [];

        foreach ($results as $result) {
            $columnName = $result['name'];

            $length = null;
            $nullable = !$result['not_null'];
            $precision = null;
            $unsigned = false;
            if (preg_match('/^(unsigned)?\s*(decimal|numeric)(?:\(([0-9]+),([0-9]+)\))?/i', $result['type'], $match)) {
                $unsigned = (bool) $match[1];
                $type = strtolower($match[2]);

                if (count($match) > 3) {
                    $length = (int) $match[3];
                    $precision = (int) $match[4];
                }

            } else if (preg_match('/^(unsigned)?\s*(tinyint|smallint|mediumint|integer|int|bigint)(?:\(([0-9]+)\))?/i', $result['type'], $match)) {
                $unsigned = (bool) $match[1];
                $type = strtolower($match[2]);

                if (count($match) > 3) {
                    $length = (int) $match[3];
                }

                $precision = 0;
            } else if (preg_match('/^(unsigned)?\s*(float|real|double)/i', $result['type'], $match)) {
                $unsigned = (bool) $match[1];
                $type = strtolower($match[2]);
            } else if (preg_match('/^(char|varchar)\(([0-9]+)\)/i', $result['type'], $match)) {
                $type = strtolower($match[1]);
                $length = (int) $match[2];
            } else {
                $type = strtolower($result['type']);
            }

            if ($result['pk'] && $primaryKeys === []) {
                $nullable = false;
            }

            if ($result['pk']) {
                $primaryKeys[] = $columnName;
            }

            $columns[$columnName] = [
                'type' => $type,
                'length' => $length,
                'precision' => $precision,
                'nullable' => $nullable,
                'unsigned' => $unsigned,
                'default' => $result['col_default'],
                'autoIncrement' => false,
            ];
        }

        if (count($primaryKeys) === 1) {
            [$primaryKey] = $primaryKeys;
            $columns[$primaryKey]['nullable'] = false;
            $columns[$primaryKey]['autoIncrement'] = true;
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
        $binder = new ValueBinder();
        $p0 = $binder->bind($this->tableName);

        $connection = $this->schema->getConnection();

        $results = $connection
            ->select([
                'ForeignKeys.id',
                'column_name' => 'ForeignKeys."from"',
                'ref_table_name' => 'ForeignKeys."table"',
                'ref_column' => 'ForeignKeys."to"',
                'ForeignKeys.on_update',
                'ForeignKeys.on_delete',
            ])
            ->from([
                'ForeignKeys' => 'PRAGMA_FOREIGN_KEY_LIST('.$p0.')',
            ])
            ->orderBy([
                'ForeignKeys.seq' => 'ASC',
            ])
            ->execute($binder)
            ->all();

        $tempForeignKeys = [];

        foreach ($results as $result) {
            $id = $result['id'];

            $tempForeignKeys[$id] ??= [
                'columns' => [],
                'referencedTable' => $result['ref_table_name'],
                'referencedColumns' => [],
                'update' => $result['on_update'],
                'delete' => $result['on_delete'],
            ];

            $tempForeignKeys[$id]['columns'][] = $result['column_name'];
            $tempForeignKeys[$id]['referencedColumns'][] = $result['ref_column'];
        }

        $foreignKeys = [];

        foreach ($tempForeignKeys as $tempForeignKey) {
            $foreignKeyName = $this->tableName.'_'.implode('_', $tempForeignKey['columns']);

            $foreignKeys[$foreignKeyName] = $tempForeignKey;
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
        $p0 = $binder->bind($this->tableName);

        $connection = $this->schema->getConnection();

        $indexes = [];

        $primaryColumns = $connection->select([
            'Columns.name',
        ])
            ->from([
                'Columns' => 'PRAGMA_TABLE_INFO('.$p0.')',
            ])
            ->where([
                'Columns.pk',
            ])
            ->execute($binder)
            ->all();

        if ($primaryColumns !== []) {
            $indexes['primary'] = [
                'columns' => array_column($primaryColumns, 'name'),
                'unique' => true,
                'primary' => true,
            ];
        }

        $binder = new ValueBinder();
        $p0 = $binder->bind($this->tableName);

        $results = $connection
            ->select([
                'Indexes.name',
                'Indexes."unique"',
            ])
            ->from([
                'Indexes' => 'PRAGMA_INDEX_LIST('.$p0.')',
            ])
            ->where([
                'Indexes.name NOT LIKE' => 'sqlite_%',
            ])
            ->orderBy([
                'Indexes.seq' => 'ASC',
            ])
            ->execute($binder)
            ->all();

        foreach ($results as $result) {
            $indexName = $result['name'];

            $binder = new ValueBinder();
            $p0 = $binder->bind($indexName);

            $columns = $connection
                ->select([
                    'name',
                ])
                ->from([
                    'PRAGMA_INDEX_INFO('.$p0.')',
                ])
                ->execute($binder)
                ->all();

            $indexes[$indexName] = [
                'columns' => array_column($columns, 'name'),
                'unique' => (bool) $result['unique'],
                'primary' => false,
            ];
        }

        return $indexes;
    }
}
