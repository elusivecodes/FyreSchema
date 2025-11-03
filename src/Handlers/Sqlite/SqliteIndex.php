<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Sqlite;

use Fyre\Schema\Index;

/**
 * SqliteIndex
 */
class SqliteIndex extends Index
{
    /**
     * New SqliteIndex constructor.
     *
     * @param SqliteTable $table The Table.
     * @param string $name The index name.
     * @param array $columns The index columns.
     * @param bool $unique Whether the index is unique.
     * @param bool $primary Whether the index is primary.
     * @param string|null $type The index type.
     */
    public function __construct(
        SqliteTable $table,
        string $name,
        array $columns = [],
        bool $unique = false,
        bool $primary = false,
    ) {
        parent::__construct($table, $name, $columns, $unique, $primary);
    }
}
