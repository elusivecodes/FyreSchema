<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableSchema;

use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

final class TableSchemaTest extends TestCase
{
    use ColumnTestTrait;
    use DefaultValueTestTrait;
    use ForeignKeyTestTrait;
    use IndexTestTrait;
    use SqliteConnectionTrait;
    use TypeTestTrait;

    public function testGetSchema(): void
    {
        $this->assertSame(
            $this->schema,
            $this->schema
                ->describe('test')
                ->getSchema()
        );
    }

    public function testGetTableName(): void
    {
        $this->assertSame(
            'test',
            $this->schema
                ->describe('test')
                ->getTableName()
        );
    }
}
