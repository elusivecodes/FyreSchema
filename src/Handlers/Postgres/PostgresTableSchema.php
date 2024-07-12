<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Postgres;

use Fyre\Schema\TableSchema;

use function array_key_exists;
use function is_numeric;
use function preg_match;
use function preg_replace;

/**
 * PostgresTableSchema
 */
class PostgresTableSchema extends TableSchema
{
    protected static array $typeMap = [
        'character varying' => 'varchar',
        'time without time zone' => 'time',
        'timestamp with time zone' => 'timestamptz',
        'timestamp without time zone' => 'timestamp',
    ];

    protected static array $types = [
        'bigint' => 'integer',
        'boolean' => 'boolean',
        'date' => 'date',
        'datetime' => 'datetime',
        'double precision' => 'float',
        'integer' => 'integer',
        'json' => 'json',
        'numeric' => 'decimal',
        'real' => 'float',
        'smallint' => 'integer',
        'time' => 'time',
        'timestamp' => 'datetime-fractional',
        'timestamptz' => 'datetime-timezone',
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
                'name' => 'Columns.column_name',
                'type' => 'Columns.data_type',
                'char_length' => 'Columns.character_maximum_length',
                'precision' => 'Columns.numeric_precision',
                'scale' => 'Columns.numeric_scale',
                'datetime_precision' => 'Columns.datetime_precision',
                'nullable' => 'Columns.is_nullable',
                'col_default' => 'Columns.column_default',
                'comment' => 'Descriptions.description',
                'auto_increment' => 'pg_get_serial_sequence(Attributes.attrelid::regclass::text, Attributes.attname) IS NOT NULL',
            ])
            ->from([
                'Columns' => 'information_schema.columns',
            ])
            ->join([
                [
                    'table' => 'pg_catalog.pg_namespace',
                    'alias' => 'Namespaces',
                    'type' => 'INNER',
                    'conditions' => [
                        'Namespaces.nspname = Columns.table_schema',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_class',
                    'alias' => 'Classes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Classes.relnamespace = Namespaces.oid',
                        'Classes.relname = Columns.table_name',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_attribute',
                    'alias' => 'Attributes',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Attributes.attrelid = Classes.oid',
                        'Attributes.attname = Columns.column_name',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_description',
                    'alias' => 'Descriptions',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Descriptions.objoid = Classes.oid',
                        'Descriptions.objsubid = Columns.ordinal_position',
                    ],
                ],
            ])
            ->where([
                'Columns.table_catalog' => $this->schema->getDatabaseName(),
                'Columns.table_schema' => $this->schema->getConnection()->getSchema(),
                'Columns.table_name' => $this->tableName,
            ])
            ->orderBy([
                'Columns.ordinal_position' => 'ASC',
            ])
            ->execute()
            ->all();

        $columns = [];

        foreach ($results as $result) {
            $columnName = $result['name'];

            $type = $result['type'];

            if (array_key_exists($type, static::$typeMap)) {
                $type = static::$typeMap[$type];
            }

            $length = null;
            $precision = null;
            switch ($type) {
                case 'date':
                case 'time':
                case 'timestamp':
                case 'timestamptz':
                    $precision = $result['datetime_precision'];
                    break;
                case 'bigint':
                case 'bigserial':
                case 'double precision':
                case 'integer':
                case 'numeric':
                case 'serial':
                case 'smallint':
                case 'real':
                    $length = $result['precision'];
                    $precision = $result['scale'];
                    break;
                default:
                    $length = $result['char_length'];
                    break;
            }

            $default = null;
            if ($result['col_default'] === null || is_numeric($result['col_default'])) {
                $default = $result['col_default'];
            } else if (preg_match('/^(?:nextval|NULL::)/', $result['col_default'])) {
                $default = 'NULL';
            } else {
                $default = preg_replace('/^(\'.*\')(?:::.*)$/', '$1', $result['col_default']);
            }

            $columns[$columnName] = [
                'type' => $type,
                'length' => $length,
                'precision' => $precision,
                'nullable' => $result['nullable'] === 'YES',
                'default' => $default,
                'comment' => $result['comment'] ?? '',
                'autoIncrement' => (bool) $result['auto_increment'],
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
                'name' => 'Constraints.conname',
                'column_name' => 'Attributes.attname',
                'ref_table_name' => 'Constraints.confrelid::regclass',
                'ref_column' => 'Attributes2.attname',
                'on_update' => 'Constraints.confupdtype',
                'on_delete' => 'Constraints.confdeltype',
            ])
            ->from([
                'Namespaces' => 'pg_catalog.pg_namespace',
            ])
            ->join([
                [
                    'table' => 'pg_catalog.pg_class',
                    'alias' => 'Classes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Classes.relnamespace = Namespaces.oid',
                        'Classes.relname' => $this->tableName,
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_constraint',
                    'alias' => 'Constraints',
                    'type' => 'INNER',
                    'conditions' => [
                        'Constraints.connamespace = Namespaces.oid',
                        'Constraints.conrelid = Classes.oid',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_attribute',
                    'alias' => 'Attributes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Attributes.attrelid = Classes.oid',
                        'Attributes.attnum = ANY(Constraints.conkey)',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_attribute',
                    'alias' => 'Attributes2',
                    'type' => 'INNER',
                    'conditions' => [
                        'Attributes2.attrelid = Classes.oid',
                        'Attributes2.attnum = ANY(Constraints.confkey)',
                    ],
                ],
            ])
            ->where([
                'Namespaces.nspname' => $this->schema->getConnection()->getSchema(),
            ])
            ->orderBy([
                'Constraints.conname' => 'ASC',
                'Attributes.attname' => 'ASC',
                'Attributes2.attnum' => 'DESC',
            ])
            ->execute()
            ->all();

        $foreignKeys = [];

        foreach ($results as $result) {
            $constraintName = $result['name'];

            if (!array_key_exists($constraintName, $foreignKeys)) {
                $foreignKeys[$constraintName] = [
                    'columns' => [],
                    'referencedTable' => $result['ref_table_name'],
                    'referencedColumns' => [],
                    'update' => match ($result['on_update']) {
                        'a' => 'NO ACTION',
                        'c' => 'CASCADE',
                        'r' => 'RESTRICT',
                        default => 'SET NULL',
                    },
                    'delete' => match ($result['on_delete']) {
                        'a' => 'NO ACTION',
                        'c' => 'CASCADE',
                        'r' => 'RESTRICT',
                        default => 'SET NULL',
                    },
                ];
            }

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
        $results = $this->schema->getConnection()
            ->select([
                'name' => 'Classes2.relname',
                'column_name' => 'Attributes.attname',
                'is_unique' => 'Indexes.indisunique',
                'is_primary' => 'Indexes.indisprimary',
            ])
            ->from([
                'Namespaces' => 'pg_catalog.pg_namespace',
            ])
            ->join([
                [
                    'table' => 'pg_catalog.pg_class',
                    'alias' => 'Classes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Classes.relnamespace = Namespaces.oid',
                        'Classes.relname' => $this->tableName,
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_index',
                    'alias' => 'Indexes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Indexes.indrelid = Classes.oid',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_class',
                    'alias' => 'Classes2',
                    'type' => 'INNER',
                    'conditions' => [
                        'Classes2.oid = Indexes.indexrelid',
                    ],
                ],
                [
                    'table' => 'pg_catalog.pg_attribute',
                    'alias' => 'Attributes',
                    'type' => 'INNER',
                    'conditions' => [
                        'Attributes.attrelid = Classes.oid',
                        'Attributes.attrelid::regclass = Indexes.indrelid::regclass',
                        'Attributes.attnum = ANY(Indexes.indkey)',
                    ],
                ],
            ])
            ->where([
                'Namespaces.nspname' => $this->schema->getConnection()->getSchema(),
            ])
            ->orderBy([
                'Indexes.indisprimary' => 'DESC',
                'Indexes.indisunique' => 'DESC',
                'Classes.relname' => 'ASC',
                'Attributes.attnum' => 'ASC',
            ])
            ->execute()
            ->all();

        $indexes = [];

        foreach ($results as $result) {
            $indexName = $result['name'];

            $indexes[$indexName] ??= [
                'columns' => [],
                'unique' => (bool) $result['is_unique'],
                'primary' => (bool) $result['is_primary'],
            ];

            $indexes[$indexName]['columns'][] = $result['column_name'];
        }

        return $indexes;
    }
}
