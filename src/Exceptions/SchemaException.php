<?php
declare(strict_types=1);

namespace Fyre\Schema\Exceptions;

use RunTimeException;

/**
 * SchemaException
 */
class SchemaException extends RunTimeException
{
    public static function forInvalidColumn(string $tableName, string $columnName): static
    {
        return new static('Invalid table column: '.$tableName.'.'.$columnName);
    }

    public static function forInvalidforeignKey(string $tableName, string $foreignKeyName): static
    {
        return new static('Invalid table foreign key: '.$tableName.'.'.$foreignKeyName);
    }

    public static function forInvalidIndex(string $tableName, string $indexName): static
    {
        return new static('Invalid table index: '.$tableName.'.'.$indexName);
    }

    public static function forInvalidTable(string $name): static
    {
        return new static('Invalid table schema: '.$name);
    }

    public static function forMissingHandler(string $name): static
    {
        return new static('Missing handler for connection handler: '.$name);
    }
}
