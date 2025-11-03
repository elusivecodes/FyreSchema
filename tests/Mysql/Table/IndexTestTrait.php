<?php
declare(strict_types=1);

namespace Tests\Mysql\Table;

use Fyre\Collection\Collection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Index;

trait IndexTestTrait
{
    public function testDebug(): void
    {
        $data = $this->schema
            ->table('test')
            ->index('name')
            ->__debugInfo();

        $this->assertSame(
            [
                'name' => 'name',
                'columns' => [
                    'name',
                ],
                'unique' => true,
                'primary' => false,
                'type' => 'btree',
            ],
            $data
        );
    }

    public function testHasIndex(): void
    {
        $this->assertTrue(
            $this->schema
                ->table('test')
                ->hasIndex('name')
        );
    }

    public function testHasIndexInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->table('test')
                ->hasIndex('invalid')
        );
    }

    public function testIndex(): void
    {
        $index = $this->schema
            ->table('test')
            ->index('name');

        $this->assertInstanceOf(Index::class, $index);

        $this->assertSame(
            'name',
            $index->getName()
        );

        $this->assertSame(
            [
                'name',
            ],
            $index->getColumns()
        );

        $this->assertTrue(
            $index->isUnique()
        );

        $this->assertFalse(
            $index->isPrimary()
        );

        $this->assertSame(
            'btree',
            $index->getType()
        );
    }

    public function testIndexes(): void
    {
        $indexes = $this->schema
            ->table('test')
            ->indexes();

        $this->assertInstanceOf(Collection::class, $indexes);

        $this->assertSame(
            [
                'PRIMARY' => [
                    'name' => 'PRIMARY',
                    'columns' => [
                        'id',
                    ],
                    'unique' => true,
                    'primary' => true,
                    'type' => 'btree',
                ],
                'name' => [
                    'name' => 'name',
                    'columns' => [
                        'name',
                    ],
                    'unique' => true,
                    'primary' => false,
                    'type' => 'btree',
                ],
                'name_value' => [
                    'name' => 'name_value',
                    'columns' => [
                        'name',
                        'value',
                    ],
                    'unique' => false,
                    'primary' => false,
                    'type' => 'btree',
                ],
            ],
            $indexes->map(
                fn(Index $index): array => $index->toArray()
            )->toArray()
        );
    }

    public function testIndexInvalid(): void
    {
        $this->expectException(SchemaException::class);

        $this->schema
            ->table('test')
            ->index('invalid');
    }

    public function testPrimaryKey(): void
    {
        $this->assertSame(
            [
                'id',
            ],
            $this->schema
                ->table('test')
                ->primaryKey()
        );
    }
}
