<?php
declare(strict_types=1);

namespace Tests\Postgres\Table;

use PHPUnit\Framework\TestCase;
use Tests\Postgres\PostgresConnectionTrait;

final class TableTest extends TestCase
{
    use ColumnTestTrait;
    use DefaultValueTestTrait;
    use ForeignKeyTestTrait;
    use IndexTestTrait;
    use PostgresConnectionTrait;
    use TypeTestTrait;

    public function testDebug(): void
    {
        $data = $this->schema
            ->table('test')
            ->__debugInfo();

        $this->assertSame(
            [
                'columns' => null,
                'foreignKeys' => null,
                'indexes' => null,
                'name' => 'test',
                'comment' => '',
            ],
            $data
        );
    }

    public function testGetSchema(): void
    {
        $this->assertSame(
            $this->schema,
            $this->schema
                ->table('test')
                ->getSchema()
        );
    }
}
