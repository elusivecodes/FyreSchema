<?php
declare(strict_types=1);

namespace Tests\Mysql\Table;

use Fyre\Collection\Collection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\Mysql\MysqlColumn;

trait ColumnTestTrait
{
    public function testColumn(): void
    {
        $column = $this->schema
            ->table('test')
            ->column('name');

        $this->assertInstanceOf(
            MysqlColumn::class,
            $column
        );

        $this->assertSame(
            'name',
            $column->getName()
        );

        $this->assertSame(
            'varchar',
            $column->getType()
        );

        $this->assertSame(
            255,
            $column->getLength()
        );

        $this->assertNull(
            $column->getPrecision()
        );

        $this->assertNull(
            $column->getValues()
        );

        $this->assertTrue(
            $column->isNullable()
        );

        $this->assertFalse(
            $column->isUnsigned()
        );

        $this->assertSame(
            'NULL',
            $column->getDefault()
        );

        $this->assertSame(
            'utf8mb4',
            $column->getCharset()
        );

        $this->assertSame(
            'utf8mb4_unicode_ci',
            $column->getCollation()
        );

        $this->assertSame(
            '',
            $column->getComment()
        );

        $this->assertFalse(
            $column->isAutoIncrement()
        );
    }

    public function testColumnDebug(): void
    {
        $data = $this->schema
            ->table('test')
            ->column('name')
            ->__debugInfo();

        $this->assertSame(
            [
                'name' => 'name',
                'type' => 'varchar',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => '',
                'autoIncrement' => false,
                'values' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            $data
        );
    }

    public function testColumnInvalid(): void
    {
        $this->expectException(SchemaException::class);

        $this->schema
            ->table('test')
            ->column('invalid');
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
                'date_precision',
                'created',
                'modified',
            ],
            $this->schema->table('test')
                ->columnNames()
        );
    }

    public function testColumns(): void
    {
        $columns = $this->schema
            ->table('test')
            ->columns();

        $this->assertInstanceOf(Collection::class, $columns);

        $this->assertSame(
            [
                'id' => [
                    'name' => 'id',
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
                    'name' => 'name',
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
                    'name' => 'value',
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
                    'name' => 'price',
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
                    'name' => 'text',
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
                    'name' => 'test',
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
                    'name' => 'bool',
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
                    'name' => 'date_precision',
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
                    'name' => 'created',
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
                    'name' => 'modified',
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
            $columns->map(
                fn(MysqlColumn $column): array => $column->toArray()
            )->toArray()
        );
    }

    public function testHasAutoIncrement(): void
    {
        $this->assertTrue(
            $this->schema
                ->table('test')
                ->hasAutoIncrement()
        );
    }

    public function testHasColumn(): void
    {
        $this->assertTrue(
            $this->schema
                ->table('test')
                ->hasColumn('name')
        );
    }

    public function testHasColumnInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->table('test')
                ->hasColumn('invalid')
        );
    }
}
