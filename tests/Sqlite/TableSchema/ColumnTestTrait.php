<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableSchema;

trait ColumnTestTrait
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
                    'type' => 'integer',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => null,
                    'autoIncrement' => true,
                ],
                'name' => [
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'autoIncrement' => false,
                ],
                'value' => [
                    'type' => 'integer',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '5',
                    'autoIncrement' => false,
                ],
                'price' => [
                    'type' => 'numeric',
                    'length' => 10,
                    'precision' => 2,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '2.50',
                    'autoIncrement' => false,
                ],
                'text' => [
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'default\'',
                    'autoIncrement' => false,
                ],
                'bool' => [
                    'type' => 'boolean',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'FALSE',
                    'autoIncrement' => false,
                ],
                'created' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'autoIncrement' => false,
                ],
                'modified' => [
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
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
