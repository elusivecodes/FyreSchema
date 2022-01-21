<?php
declare(strict_types=1);

namespace Tests\TableSchema;

use
    Fyre\DB\Types\DateTimeType,
    Fyre\DB\Types\DecimalType,
    Fyre\DB\Types\StringType;

trait TypeTest
{

    public function testGetType(): void
    {
        $this->assertInstanceOf(
            StringType::class,
            $this->schema->describe('test')
                ->getType('name')
        );
    }

    public function testGetTypeDateTime(): void
    {
        $this->assertInstanceOf(
            DateTimeType::class,
            $this->schema->describe('test')
                ->getType('created')
        );
    }

    public function testGetTypeDecimal(): void
    {
        $this->assertInstanceOf(
            DecimalType::class,
            $this->schema->describe('test')
                ->getType('price')
        );
    }

}
