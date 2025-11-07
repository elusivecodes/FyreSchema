<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Schema\Column;
use Fyre\Schema\ForeignKey;
use Fyre\Schema\Index;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;
use Fyre\Schema\Table;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

final class SchemaRegistryTest extends TestCase
{
    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(SchemaRegistry::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Schema::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Table::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Column::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(ForeignKey::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Index::class)
        );
    }
}
