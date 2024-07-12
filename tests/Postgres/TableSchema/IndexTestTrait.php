<?php
declare(strict_types=1);

namespace Tests\Postgres\TableSchema;

trait IndexTestTrait
{
    public function testHasIndex(): void
    {
        $this->assertTrue(
            $this->schema
                ->describe('test')
                ->hasIndex('name')
        );
    }

    public function testHasIndexInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->describe('test')
                ->hasIndex('invalid')
        );
    }

    public function testIndex(): void
    {
        $this->assertSame(
            [
                'columns' => [
                    'name',
                ],
                'unique' => true,
                'primary' => false,
            ],
            $this->schema
                ->describe('test')
                ->index('name')
        );
    }

    public function testIndexes(): void
    {
        $this->assertSame(
            [
                'test_pkey' => [
                    'columns' => [
                        'id',
                    ],
                    'unique' => true,
                    'primary' => true,
                ],
                'name' => [
                    'columns' => [
                        'name',
                    ],
                    'unique' => true,
                    'primary' => false,
                ],
                'name_value' => [
                    'columns' => [
                        'name',
                        'value',
                    ],
                    'unique' => false,
                    'primary' => false,
                ],
            ],
            $this->schema
                ->describe('test')
                ->indexes()
        );
    }

    public function testIndexInvalid(): void
    {
        $this->assertNull(
            $this->schema
                ->describe('test')
                ->index('invalid')
        );
    }

    public function testPrimaryKey(): void
    {
        $this->assertSame(
            [
                'id',
            ],
            $this->schema
                ->describe('test')
                ->primaryKey()
        );
    }
}
