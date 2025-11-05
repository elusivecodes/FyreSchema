<?php
declare(strict_types=1);

namespace Tests\Mysql\Table;

use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class TableTest extends TestCase
{
    use ColumnTestTrait;
    use DefaultValueTestTrait;
    use ForeignKeyTestTrait;
    use IndexTestTrait;
    use MysqlConnectionTrait;
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
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
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
