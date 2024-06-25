<?php
declare(strict_types=1);

namespace Fyre\Schema\Exceptions;

use RunTimeException;

/**
 * SchemaException
 */
class SchemaException extends RunTimeException
{
    public static function forInvalidTable(string $name): static
    {
        return new static('Invalid table schema: '.$name);
    }

    public static function forMissingHandler(string $name): static
    {
        return new static('Missing handler for connection handler: '.$name);
    }
}
