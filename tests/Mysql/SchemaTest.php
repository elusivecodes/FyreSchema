<?php
declare(strict_types=1);

namespace Tests\Mysql;

use Fyre\Collection\Collection;
use Fyre\Schema\Exceptions\SchemaException;
use Fyre\Schema\Handlers\Mysql\MysqlTable;
use PHPUnit\Framework\TestCase;

final class SchemaTest extends TestCase
{
    use MysqlConnectionTrait;

    public function testDebug(): void
    {
        $data = $this->schema->__debugInfo();

        $this->assertSame(
            [
                'tables' => null,
            ],
            $data
        );
    }

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
        $table = $this->schema->table('test');

        $this->assertInstanceOf(MysqlTable::class, $table);

        $this->assertSame(
            'test',
            $table->getName()
        );

        $this->assertSame(
            'InnoDB',
            $table->getEngine()
        );

        $this->assertSame(
            '',
            $table->getComment()
        );

        $this->assertSame(
            'utf8mb4',
            $table->getCharset()
        );

        $this->assertSame(
            'utf8mb4_unicode_ci',
            $table->getCollation()
        );
    }

    public function testTableInvalid(): void
    {
        $this->expectException(SchemaException::class);

        $this->schema->table('invalid');
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
        $tables = $this->schema->tables();

        $this->assertInstanceOf(Collection::class, $tables);

        $this->assertSame(
            [
                'test' => [
                    'name' => 'test',
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                ],
                'test_values' => [
                    'name' => 'test_values',
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'comment' => '',
                ],
            ],
            $tables->map(
                fn(MysqlTable $table): array => $table->toArray()
            )->toArray()
        );
    }
}
