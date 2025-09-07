<?php
declare(strict_types=1);

namespace Tests\Sqlite\Table;

use Fyre\Collection\Collection;
use Fyre\Schema\Column;
use Fyre\Schema\Exceptions\SchemaException;

trait ColumnTestTrait
{
    public function testColumn(): void
    {
        $column = $this->schema
            ->table('test')
            ->column('name');

        $this->assertInstanceOf(Column::class, $column);

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

        $this->assertNull(
            $column->getComment()
        );

        $this->assertFalse(
            $column->isAutoIncrement()
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
                    'length' => null,
                    'precision' => 0,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => null,
                    'comment' => null,
                    'autoIncrement' => true,
                ],
                'name' => [
                    'name' => 'name',
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'NULL',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'value' => [
                    'name' => 'value',
                    'type' => 'integer',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '5',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'price' => [
                    'name' => 'price',
                    'type' => 'numeric',
                    'length' => 10,
                    'precision' => 2,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '2.50',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'text' => [
                    'name' => 'text',
                    'type' => 'varchar',
                    'length' => 255,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => '\'default\'',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'bool' => [
                    'name' => 'bool',
                    'type' => 'boolean',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'FALSE',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'created' => [
                    'name' => 'created',
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'nullable' => false,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
                'modified' => [
                    'name' => 'modified',
                    'type' => 'datetime',
                    'length' => null,
                    'precision' => null,
                    'nullable' => true,
                    'unsigned' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => null,
                    'autoIncrement' => false,
                ],
            ],
            $columns->map(
                fn(Column $column): array => $column->toArray()
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
