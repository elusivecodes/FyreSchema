<?php
declare(strict_types=1);

namespace Tests\TableSchema;

use Fyre\DB\Types\BooleanType;
use Fyre\DB\Types\DateTimeType;
use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\StringType;

trait TypeTestTrait
{
    public function testGetType(): void
    {
        $this->assertInstanceOf(
            StringType::class,
            $this->schema->describe('test')
                ->getType('name')
        );
    }

    public function testGetTypeBoolean(): void
    {
        $this->assertInstanceOf(
            BooleanType::class,
            $this->schema->describe('test')
                ->getType('bool')
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
