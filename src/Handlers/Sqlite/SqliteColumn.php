<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\Schema\Column;

/**
 * SqliteColumn
 */
class SqliteColumn extends Column
{
    protected static array $types = [
        'bigint' => 'integer',
        'binary' => 'binary',
        'blob' => 'binary',
        'boolean' => 'boolean',
        'clob' => 'binary',
        'date' => 'date',
        'datetime' => 'datetime',
        'datetimefractional' => 'datetime-fractional',
        'decimal' => 'decimal',
        'double' => 'float',
        'float' => 'float',
        'int' => 'integer',
        'integer' => 'integer',
        'json' => 'json',
        'mediumint' => 'integer',
        'numeric' => 'decimal',
        'real' => 'float',
        'smallint' => 'integer',
        'text' => 'text',
        'time' => 'time',
        'timestamp' => 'datetime',
        'timestampfractional' => 'datetime-fractional',
        'timestamptimezone' => 'datetime-timezone',
        'tinyint' => 'integer',
        'varbinary' => 'binary',
    ];
}
