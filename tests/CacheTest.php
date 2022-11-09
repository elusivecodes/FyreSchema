<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Cache\Cache,
    PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{

    use
        ConnectionTrait;

    public function testCacheTables(): void
    {
        $this->schema->tables();

        $this->assertSame(
            [
                'test' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => ''
                ],
                'test_values' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => ''
                ]
            ],
            Cache::use('schema')->get('default.test.tables')
        );
    }

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
                    'extra' => 'auto_increment',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
                ],
                'test' => [
                    'type' => 'enum',
                    'length' => null,
                    'precision' => null,
                    'values' => [
                        'Y',
                        'N'
                    ],
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'Y\'',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => '',
                    'comment' => ''
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
                    'extra' => 'on update current_timestamp()',
                    'comment' => ''
                ]
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
                        'test_id'
                    ],
                    'referencedTable' => 'test',
                    'referencedColumns' => [
                        'id'
                    ],
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
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
                'name' => [
                    'columns' => [
                        'name'
                    ],
                    'unique' => true,
                    'type' => 'BTREE',
                    'foreignKey' => false
                ],
                'name_value' => [
                    'columns' => [
                        'name',
                        'value'
                    ],
                    'unique' => false,
                    'type' => 'BTREE',
                    'foreignKey' => false
                ],
                'PRIMARY' => [
                    'columns' => [
                        'id'
                    ],
                    'unique' => true,
                    'type' => 'BTREE',
                    'foreignKey' => false
                ]
            ],
            Cache::use('schema')->get('default.test.test.indexes')
        );
    }

}
