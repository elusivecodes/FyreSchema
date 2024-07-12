<?php
declare(strict_types=1);

namespace Tests\Postgres\TableSchema;

use PHPUnit\Framework\TestCase;
use Tests\Postgres\PostgresConnectionTrait;

final class TableSchemaTest extends TestCase
{
    use ColumnTestTrait;
    use DefaultValueTestTrait;
    use ForeignKeyTestTrait;
    use IndexTestTrait;
    use PostgresConnectionTrait;
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
