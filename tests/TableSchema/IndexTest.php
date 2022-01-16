<?php
declare(strict_types=1);

namespace Tests\TableSchema;

trait IndexTest
{

    public function testIndex(): void
    {
        $this->assertSame(
            [
                'columns' => [
                    'name'
                ],
                'unique' => true,
                'type' => 'BTREE',
                'foreignKey' => false
            ],
            $this->schema
                ->describe('test')
                ->index('name')
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

    public function testIndexes(): void
    {
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
            $this->schema
                ->describe('test')
                ->indexes()
        );
    }

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

    public function testPrimaryKey(): void
    {
        $this->assertSame(
            [
                'id'
            ],
            $this->schema
                ->describe('test')
                ->primaryKey()
        );
    }

}
