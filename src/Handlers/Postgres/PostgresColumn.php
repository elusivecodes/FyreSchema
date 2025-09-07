<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Postgres;

use Fyre\Schema\Column;

/**
 * PostgresColumn
 */
class PostgresColumn extends Column
{
    protected static array $types = [
        'bigint' => 'integer',
        'boolean' => 'boolean',
        'bytea' => 'binary',
        'date' => 'date',
        'datetime' => 'datetime',
        'double precision' => 'float',
        'integer' => 'integer',
        'json' => 'json',
        'jsonb' => 'json',
        'numeric' => 'decimal',
        'real' => 'float',
        'smallint' => 'integer',
        'text' => 'text',
        'time without time zone' => 'time',
        'timestamp without time zone' => 'datetime-fractional',
        'timestamp with time zone' => 'datetime-timezone',
    ];
}
