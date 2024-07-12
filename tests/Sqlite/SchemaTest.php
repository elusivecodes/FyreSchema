<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use PHPUnit\Framework\TestCase;

final class SchemaTest extends TestCase
{
    use SqliteConnectionTrait;

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
            ':memory:',
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
            [],
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
                'test' => [],
                'test_values' => [],
            ],
            $this->schema->tables()
        );
    }
}
