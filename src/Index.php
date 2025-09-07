<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Utility\Traits\MacroTrait;

/**
 * Index
 */
class Index
{
    use MacroTrait;

    /**
     * New Index constructor.
     *
     * @param Table $table The Table.
     * @param string $name The index name.
     * @param array $columns The index columns.
     * @param bool $unique Whether the index is unique.
     * @param bool $primary Whether the index is primary.
     * @param string|null $type The index type.
     */
    public function __construct(
        protected Table $table,
        protected string $name,
        protected array $columns = [],
        protected bool $unique = false,
        protected bool $primary = false,
        protected string|null $type = null,
    ) {}

    /**
     * Get the index columns.
     *
     * @return array The index columns.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the index name.
     *
     * @return string The index name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Table.
     *
     * @return Table The Table.
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * Get the index type.
     *
     * @return string|null The index type.
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Determine whether the index is primary.
     *
     * @return bool TRUE if the index is primary, otherwise FALSE.
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * Determine whether the index is unique.
     *
     * @return bool TRUE if the index is unique, otherwise FALSE.
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Get the index data as an array.
     *
     * @return array The index data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'unique' => $this->unique,
            'primary' => $this->primary,
            'type' => $this->type,
        ];
    }
}
