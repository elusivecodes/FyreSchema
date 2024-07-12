<?php
declare(strict_types=1);

namespace Tests\Postgres;

use PHPUnit\Framework\TestCase;

final class SchemaTest extends TestCase
{
    use PostgresConnectionTrait;

    public function testGetConnection(): void
    {
        $this->assertSame(
            $this->db,
            $this->schema->getConnection()
        );
    }

    public function testGetDatabaseName(): void
    {
        $this->assertSame(
            'test',
            $this->schema->getDatabaseName()
        );
    }

    public function testHasTable(): void
    {
        $this->assertTrue(
            $this->schema->hasTable('test_values')
        );
    }

    public function testHasTableInvalid(): void
    {
        $this->assertFalse(
            $this->schema->hasTable('invalid')
        );
    }

    public function testTable(): void
    {
        $this->assertSame(
            [
                'comment' => '',
            ],
            $this->schema->table('test')
        );
    }

    public function testTableInvalid(): void
    {
        $this->assertNull(
            $this->schema->table('invalid')
        );
    }

    public function testTableNames(): void
    {
        $this->assertSame(
            [
                'test',
                'test_values',
            ],
            $this->schema->tableNames()
        );
    }

    public function testTables(): void
    {
        $this->assertSame(
            [
                'test' => [
                    'comment' => '',
                ],
                'test_values' => [
                    'comment' => '',
                ],
            ],
            $this->schema->tables()
        );
    }
}
