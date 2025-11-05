<?php
declare(strict_types=1);

namespace Tests\Postgres\Table;

use Fyre\Collection\Collection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\Postgres\PostgresColumn;

trait ColumnTestTrait
{
    public function testColumn(): void
    {
        $column = $this->schema
            ->table('test')
            ->column('name');

        $this->assertInstanceOf(PostgresColumn::class, $column);

        $this->assertSame(
            'name',
            $column->getName()
        );

        $this->assertSame(
            'character varying',
            $column->getType()
        );

        $this->assertSame(
            255,
            $column->getLength()
        );

        $this->assertNull(
            $column->getPrecision()
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
                'type' => 'character varying',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => '',
                'autoIncrement' => false,
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
                    'type' => 'integer',
                    'length' => 11,
                    'precision' => 0,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => null,
                    'comment' => '',
                    'autoIncrement' => true,
                ],
                'name' => [
                    'name' => 'name',
                    'type' => 'character varying',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'value' => [
                    'name' => 'value',
                    'type' => 'double precision',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '5',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'price' => [
                    'name' => 'price',
                    'type' => 'numeric',
                    'length' => 10,
                    'precision' => 2,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '2.50',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'text' => [
                    'name' => 'text',
                    'type' => 'character varying',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'default\'',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'bool' => [
                    'name' => 'bool',
                    'type' => 'boolean',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'false',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'date_precision' => [
                    'name' => 'date_precision',
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'created' => [
                    'name' => 'created',
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 6,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
                'modified' => [
                    'name' => 'modified',
                    'type' => 'timestamp without time zone',
                    'length' => null,
                    'precision' => 6,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => '',
                    'autoIncrement' => false,
                ],
            ],
            $columns->map(
                fn(PostgresColumn $column): array => $column->toArray()
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
