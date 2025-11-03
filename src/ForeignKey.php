<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\Utility\Traits\MacroTrait;

use function get_object_vars;

/**
 * ForeignKey
 */
class ForeignKey
{
    use MacroTrait;

    /**
     * New ForeignKey constructor.
     *
     * @param Table $table The Table.
     * @param string $name The foreign key name.
     * @param array $columns The column names.
     * @param string $referencedTable The referenced table name.
     * @param array $referencedColumns The referenced column names.
     * @param string|null $onUpdate The action on update.
     * @param string|null $onDelete The action on delete.
     */
    public function __construct(
        protected Table $table,
        protected string $name,
        protected array $columns = [],
        protected string|null $referencedTable = null,
        protected array $referencedColumns = [],
        protected string|null $onUpdate = null,
        protected string|null $onDelete = null
    ) {}

    /**
     * Get the debug info of the object.
     *
     * @return array The debug info.
     */
    public function __debugInfo(): array
    {
        $data = get_object_vars($this);

        unset($data['table']);

        return $data;
    }

    /**
     * Get the column names.
     *
     * @return array The column names.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the foreign key name.
     *
     * @return string The foreign key name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the delete action.
     *
     * @return string|null The delete action.
     */
    public function getOnDelete(): string|null
    {
        return $this->onDelete;
    }

    /**
     * Get the update action.
     *
     * @return string|null The update action.
     */
    public function getOnUpdate(): string|null
    {
        return $this->onUpdate;
    }

    /**
     * Get the referenced column names.
     *
     * @return array The referenced column names.
     */
    public function getReferencedColumns(): array
    {
        return $this->referencedColumns;
    }

    /**
     * Get the referenced table name.
     *
     * @return string|null The referenced table name.
     */
    public function getReferencedTable(): string|null
    {
        return $this->referencedTable;
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
     * Get the foreign key data as an array.
     *
     * @return array The foreign key data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'referencedTable' => $this->referencedTable,
            'referencedColumns' => $this->referencedColumns,
            'onUpdate' => $this->onUpdate,
            'onDelete' => $this->onDelete,
        ];
    }
}
