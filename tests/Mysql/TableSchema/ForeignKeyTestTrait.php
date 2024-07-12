<?php
declare(strict_types=1);

namespace Tests\Mysql\TableSchema;

trait ForeignKeyTestTrait
{
    public function testForeignKey(): void
    {
        $this->assertSame(
            [
                'columns' => [
                    'test_id',
                ],
                'referencedTable' => 'test',
                'referencedColumns' => [
                    'id',
                ],
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ],
            $this->schema
                ->describe('test_values')
                ->foreignKey('test_values_test_id')
        );
    }

    public function testForeignKeyInvalid(): void
    {
        $this->assertNull(
            $this->schema
                ->describe('test_values')
                ->foreignKey('invalid')
        );
    }

    public function testForeignKeys(): void
    {
        $this->assertSame(
            [
                'test_values_test_id' => [
                    'columns' => [
                        'test_id',
                    ],
                    'referencedTable' => 'test',
                    'referencedColumns' => [
                        'id',
                    ],
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ],
            ],
            $this->schema
                ->describe('test_values')
                ->foreignKeys()
        );
    }

    public function testHasForeignKey(): void
    {
        $this->assertTrue(
            $this->schema
                ->describe('test_values')
                ->hasForeignKey('test_values_test_id')
        );
    }

    public function testHasForeignKeyInvalid(): void
    {
        $this->assertFalse(
            $this->schema
                ->describe('test_values')
                ->hasForeignKey('invalid')
        );
    }
}
