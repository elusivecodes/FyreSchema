<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class CacheTest extends TestCase
{
    use MysqlConnectionTrait;

    public function testCacheColumns(): void
    {
        $this->schema
            ->describe('test')
            ->columns();

        $this->assertSame(
            [
                'id' => [
                    'type' => 'int',
                    'length' => 10,
                    'precision' => 0,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => null,
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => true,
                ],
                'name' => [
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'values' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'value' => [
                    'type' => 'int',
                    'length' => 10,
                    'precision' => 0,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '5',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'price' => [
                    'type' => 'decimal',
                    'length' => 10,
                    'precision' => 2,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '2.50',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'text' => [
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'default\'',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'test' => [
                    'type' => 'enum',
                    'length' => null,
                    'precision' => null,
                    'values' => [
                        'Y',
                        'N',
                    ],
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'Y\'',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'bool' => [
                    'type' => 'tinyint',
                    'length' => 1,
                    'precision' => 0,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'date_precision' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => 6,
                    'values' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'created' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'values' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'current_timestamp()',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'modified' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'values' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'current_timestamp()',
                    'charset' => null,
                    'collation' => null,
                    'comment' => '',
                    'autoIncrement' => false,
                ],
            ],
            Cache::use('schema')->get('default.test.test.columns')
        );
    }

    public function testCacheForeignKeys(): void
    {
        $this->schema
            ->describe('test_values')
            ->foreignKeys();

        $this->assertSame(
            [
                'test_values_test_id' => [
                    'columns' => [
                        'test_id',
                    ],
                    'referencedTable' => 'test',
                    'referencedColumns' => [
                        'id',
                    ],
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ],
            ],
            Cache::use('schema')->get('default.test.test_values.foreign_keys')
        );
    }

    public function testCacheIndexes(): void
    {
        $this->schema
            ->describe('test')
            ->indexes();

        $this->assertSame(
            [
                'PRIMARY' => [
                    'columns' => [
                        'id',
                    ],
                    'unique' => true,
                    'primary' => true,
                    'type' => 'btree',
                ],
                'name' => [
                    'columns' => [
                        'name',
                    ],
                    'unique' => true,
                    'primary' => false,
                    'type' => 'btree',
                ],
                'name_value' => [
                    'columns' => [
                        'name',
                        'value',
                    ],
                    'unique' => false,
                    'primary' => false,
                    'type' => 'btree',
                ],
            ],
            Cache::use('schema')->get('default.test.test.indexes')
        );
    }

    public function testCacheTables(): void
    {
        $this->schema->tables();

        $this->assertSame(
            [
                'test' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                ],
                'test_values' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                ],
            ],
            Cache::use('schema')->get('default.test.tables')
        );
    }
}
