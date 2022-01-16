<?php
declare(strict_types=1);

namespace Tests\TableSchema;

use
    Tests\ConnectionTrait,
    PHPUnit\Framework\TestCase;

final class TableSchemaTest extends TestCase
{

    use
        ColumnTest,
        ConnectionTrait,
        DefaultValueTest,
        ForeignKeyTest,
        IndexTest;

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
