<?php
declare(strict_types=1);

namespace Tests\TableSchema;

trait DefaultValueTest
{

    public function testDefaultValue(): void
    {
        $this->assertSame(
            'default',
            $this->schema
                ->describe('test')
                ->defaultValue('text')
        );
    }

    public function testDefaultValueNull(): void
    {
        $this->assertNull(
            $this->schema
                ->describe('test')
                ->defaultValue('name')
        );
    }

    public function testDefaultValueInt(): void
    {
        $this->assertSame(
            5,
            $this->schema
                ->describe('test')
                ->defaultValue('value')
        );
    }

    public function testDefaultValueDecimal(): void
    {
        $this->assertSame(
            2.5,
            $this->schema
                ->describe('test')
                ->defaultValue('price')
        );
    }

    public function testDefaultValueExpression(): void
    {
        $this->assertMatchesRegularExpression(
            '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',
            $this->schema
                ->describe('test')
                ->defaultValue('created')
        );
    }

    public function testDefaultValueNone(): void
    {
        $this->assertSame(
            '',
            $this->schema
                ->describe('test_values')
                ->defaultValue('value')
        );
    }

    public function testDefaultValueInvalid(): void
    {
        $this->assertNull(
            $this->schema
                ->describe('test')
                ->defaultValue('invalid')
        );
    }

}
