<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\DB\TypeParser;
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

    /**
     * New SqliteColumn constructor.
     *
     * @param Table $table The Table.
     * @param TypeParser $typeParser The TypeParser.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param bool $unsigned Whether the column is unsigned.
     * @param string|null $default The column default value.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     * @param string|null $comment The column comment.
     */
    public function __construct(
        SqliteTable $table,
        TypeParser $typeParser,
        string $name,
        string $type,
        int|null $length = null,
        int|null $precision = null,
        bool $nullable = false,
        bool $unsigned = false,
        string|null $default = null,
        bool $autoIncrement = false,
    ) {
        parent::__construct(
            $table,
            $typeParser,
            $name,
            $type,
            $length,
            $precision,
            $nullable,
            $unsigned,
            $default,
            null,
            $autoIncrement
        );
    }
}
