<?php
declare(strict_types=1);

namespace Fyre\Schema;

use Fyre\DB\TypeParser;
use Fyre\DB\Types\Type;
use Fyre\Utility\Traits\MacroTrait;

use function ctype_digit;
use function is_numeric;
use function preg_match;
use function strtolower;

/**
 * Column
 */
abstract class Column
{
    use MacroTrait;

    /**
     * New Column constructor.
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
     * @param string|null $comment The column comment.
     */
    public function __construct(
        protected Table $table,
        protected TypeParser $typeParser,
        protected string $name,
        protected string $type,
        protected int|null $length = null,
        protected int|null $precision = null,
        protected bool $nullable = false,
        protected bool $unsigned = false,
        protected string|null $default = null,
        protected string|null $comment = null,
        protected bool $autoIncrement = false,
    ) {}

    /**
     * Get the default value for a column.
     *
     * @return mixed The default value.
     */
    public function defaultValue(): mixed
    {
        if (!$this->default) {
            return '';
        }

        if (strtolower($this->default) === 'null') {
            return null;
        }

        if (ctype_digit($this->default)) {
            return (int) $this->default;
        }

        if (is_numeric($this->default)) {
            return (float) $this->default;
        }

        if (preg_match('/^(["\'])(.*)\1$/', $this->default, $match)) {
            return $match[2];
        }

        return $this->table->getSchema()
            ->getConnection()
            ->rawQuery('SELECT '.$this->default)
            ->fetchColumn();
    }

    /**
     * Get the column comment.
     *
     * @return string|null The column comment.
     */
    public function getComment(): string|null
    {
        return $this->comment;
    }

    /**
     * Get the column default value.
     *
     * @return string|null The column default value.
     */
    public function getDefault(): string|null
    {
        return $this->default;
    }

    /**
     * Get the column length.
     *
     * @return int|null The column length.
     */
    public function getLength(): int|null
    {
        return $this->length;
    }

    /**
     * Get the column name.
     *
     * @return string The column name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column precision.
     *
     * @return int|null The column precision.
     */
    public function getPrecision(): int|null
    {
        return $this->precision;
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
     * Get the column type.
     *
     * @return string The column type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Determine whether the column is an auto increment column.
     *
     * @return bool TRUE if the column is an auto increment column, otherwise FALSE.
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * Determine whether the column is nullable.
     *
     * @return bool TRUE if the column is nullable, otherwise FALSE.
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Determine whether the column is unsigned.
     *
     * @return bool TRUE if the column is unsigned, otherwise FALSE.
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * Get the column data as an array.
     *
     * @return array The column data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'precision' => $this->precision,
            'nullable' => $this->nullable,
            'unsigned' => $this->unsigned,
            'default' => $this->default,
            'comment' => $this->comment,
            'autoIncrement' => $this->autoIncrement,
        ];
    }

    /**
     * Get the type parser for the column.
     *
     * @return Type The type parser for the column.
     */
    public function type(): Type
    {
        if ($this->type === 'tinyint' && $this->length == 1) {
            $type = 'boolean';
        }
        $type = static::$types[$this->type] ?? 'string';

        return $this->typeParser->use($type);
    }
}
