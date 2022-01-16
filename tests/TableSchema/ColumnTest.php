<?php
declare(strict_types=1);

namespace Tests\TableSchema;

trait ColumnTest
{

    public function testColumn(): void
    {
        $this->assertSame(
            [
                'type' => 'varchar',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'extra' => '',
                'comment' => ''
            ],
            $this->schema
                ->describe('test')
                ->column('name')
        );
    }

    public function testColumnInvalid(): void
    {
        $this->assertNull(
            $this->schema
                ->describe('test')
                ->column('invalid')
        );
    }

    public function testColumnNames(): void
    {
        $this->assertSame(
            [
                'id',
                'name',
                'value',
                'price',
                'text',
                'created',
                'modified'
            ],
            $this->schema->describe('test')->columnNames()
        );
    }

    public function testColumns(): void
    {
        $this->assertSame(
            [
                'id' => [
                    'type' => 'int',
                    'length' => 10,
                    'precision' => 0,
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
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'default\'',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'extra' => '',
                    'comment' => ''
                ],
                'created' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
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
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'current_timestamp()',
                    'charset' => null,
                    'collation' => null,
                    'extra' => 'on update current_timestamp()',
                    'comment' => ''
                ]
            ],
            $this->schema
                ->describe('test')
                ->columns()
        );
    }

    public function testHasColumn(): void
    {
        $this->assertTrue(
            $this->schema
                ->describe('test')
                ->hasColumn('name')
        );
    }

    public function testHasColumnInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->describe('test')
                ->hasColumn('invalid')
        );
    }

    public function testIsNullable(): void
    {
        $this->assertTrue(
            $this->schema
                ->describe('test')
                ->isNullable('name')
        );
    }

    public function testIsNullableFalse(): void
    {
        $this->assertFalse(
            $this->schema
                ->describe('test')
                ->isNullable('id')
        );
    }

    public function testIsNullableInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->describe('test')
                ->isNullable('invalid')
        );
    }

}
