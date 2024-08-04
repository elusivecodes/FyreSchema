<?php
declare(strict_types=1);

namespace Tests\Postgres\TableSchema;

trait ColumnTestTrait
{
    public function testColumn(): void
    {
        $this->assertSame(
            [
                'type' => 'character varying',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'default' => 'NULL',
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
                'bool',
                'date_precision',
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
                    'length' => 11,
                    'precision' => 0,
                    'nullable' => false,
                    'default' => null,
                    'comment' => '',
                    'autoIncrement' => true,
                ],
                'name' => [
                    'type' => 'character varying',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => true,
                    'default' => 'NULL',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'value' => [
                    'type' => 'double precision',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'default' => '5',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'price' => [
                    'type' => 'numeric',
                    'length' => 10,
                    'precision' => 2,
                    'nullable' => false,
                    'default' => '2.50',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'text' => [
                    'type' => 'character varying',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => false,
                    'default' => '\'default\'',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'bool' => [
                    'type' => 'boolean',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'default' => 'false',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'date_precision' => [
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => true,
                    'default' => 'NULL',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'created' => [
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 6,
                    'nullable' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'modified' => [
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 6,
                    'nullable' => true,
                    'default' => 'CURRENT_TIMESTAMP',
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
