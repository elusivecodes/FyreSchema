<?php
declare(strict_types=1);

namespace Tests\Mysql\TableSchema;

trait ColumnTestTrait
{
    public function testColumn(): void
    {
        $this->assertSame(
            [
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
                'test',
                'bool',
                'created',
                'modified',
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
            $this->schema
                ->describe('test')
                ->columns()
        );
    }

    public function testHasAutoincrement(): void
    {
        $this->assertTrue(
            $this->schema
                ->describe('test')
                ->hasAutoIncrement()
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
