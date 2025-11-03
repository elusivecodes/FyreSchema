<?php
declare(strict_types=1);

namespace Fyre\Schema\Handlers\Mysql;

use Fyre\DB\TypeParser;
use Fyre\DB\Types\Type;
use Fyre\Schema\Column;
use Override;

/**
 * MysqlColumn
 */
class MysqlColumn extends Column
{
    protected static array $types = [
        'bigint' => 'integer',
        'binary' => 'binary',
        'blob' => 'binary',
        'boolean' => 'boolean',
        'date' => 'date',
        'datetime' => 'datetime',
        'decimal' => 'decimal',
        'double' => 'float',
        'enum' => 'enum',
        'float' => 'float',
        'int' => 'integer',
        'json' => 'json',
        'longblob' => 'binary',
        'longtext' => 'text',
        'mediumblob' => 'binary',
        'mediumint' => 'integer',
        'mediumtext' => 'text',
        'set' => 'set',
        'smallint' => 'integer',
        'text' => 'text',
        'time' => 'time',
        'timestamp' => 'datetime',
        'tinyblob' => 'binary',
        'tinyint' => 'integer',
        'tinytext' => 'text',
        'varbinary' => 'binary',
    ];

    /**
     * New MysqlColumn constructor.
     *
     * @param MysqlTable $table The Table.
     * @param TypeParser $typeParser The TypeParser.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param bool $unsigned Whether the column is unsigned.
     * @param string|null $default The column default value.
     * @param string|null $comment The column comment.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     * @param array|null $values The column values.
     * @param string|null $charset The column character set.
     * @param string|null $collation The column collation.
     */
    public function __construct(
        MysqlTable $table,
        TypeParser $typeParser,
        string $name,
        string $type,
        int|null $length = null,
        int|null $precision = null,
        bool $nullable = false,
        bool $unsigned = false,
        string|null $default = null,
        string|null $comment = null,
        bool $autoIncrement = false,
        protected array|null $values = null,
        protected string|null $charset = null,
        protected string|null $collation = null,
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
            $comment,
            $autoIncrement
        );
    }

    /**
     * Get the column character set.
     *
     * @return string|null The column character set.
     */
    public function getCharset(): string|null
    {
        return $this->charset;
    }

    /**
     * Get the column collation.
     *
     * @return string|null The column collation.
     */
    public function getCollation(): string|null
    {
        return $this->collation;
    }

    /**
     * Get the column enum values.
     *
     * @return array|null The column enum values.
     */
    public function getValues(): array|null
    {
        return $this->values;
    }

    /**
     * Get the column data as an array.
     *
     * @return array The column data.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'precision' => $this->precision,
            'values' => $this->values,
            'nullable' => $this->nullable,
            'unsigned' => $this->unsigned,
            'default' => $this->default,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'comment' => $this->comment,
            'autoIncrement' => $this->autoIncrement,
        ];
    }

    /**
     * Get the type parser for the column.
     *
     * @return Type The type parser for the column.
     */
    #[Override]
    public function type(): Type
    {
        if ($this->type === 'tinyint' && $this->length == 1) {
            return $this->typeParser->use('boolean');
        }

        return parent::type();
    }
}
