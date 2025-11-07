# FyreSchema

**FyreSchema** is a free, open-source database schema library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Schemas](#schemas)
- [Tables](#tables)
    - [MySQL Tables](#mysql-tables)
- [Columns](#columns)
    - [MySQL Columns](#mysql-columns)
- [Indexes](#indexes)
- [Foreign Keys](#foreign-keys)



## Installation

**Using Composer**

```
composer require fyre/schema
```

In PHP:

```php
use Fyre\Schema\SchemaRegistry;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$schemaRegistry = new SchemaRegistry($container);
```

**Autoloading**

It is recommended to bind the *SchemaRegistry* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(SchemaRegistry::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$schemaRegistry = $container->use(SchemaRegistry::class);
```


## Methods

**Map**

Map a [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class to a [*Schema*](#schemas) handler.

- `$connectionClass` is a string representing the [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class name.
- `$schemaClass` is a string representing the [*Schema*](#schemas) class name.

```php
$schemaRegistry->map($connectionClass, $schemaClass);
```

**Use**

Load the shared [*Schema*](#schemas) for a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

- `$connection` is a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$schema = $schemaRegistry->use($connection);
```

[*Schema*](#schemas) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).


## Schemas

**Clear**

Clear the table data (including cache).

```php
$schema->clear();
```

**Get Connection**

Get the [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$connection = $schema->getConnection();
```

**Get Database Name**

Get the database name.

```php
$database = $schema->getDatabaseName();
```

**Has Table**

Determine whether the schema has a table.

- `$name` is a string representing the table name.

```php
$hasTable = $schema->hasTable($name);
```

**Table**

Load a [*Table*](#tables).

- `$name` is a string representing the table name.

```php
$table = $schema->table($name);
```

**Table Names**

Get the names of all schema tables.

```php
$tableNames = $schema->tableNames();
```

**Tables**

Load all schema tables.

```php
$tables = $schema->tables();
```

This method will return a [*Collection*](https://github.com/elusivecodes/FyreCollection) containing the loaded [tables](#tables).


## Tables

**Clear**

Clear the table data (including cache).

```php
$table->clear();
```

**Column**

Load a [*Column*](#columns).

- `$name` is a string representing the column name.

```php
$column = $table->column($name);
```

**Column Names**

Get the names of all table columns.

```php
$columnNames = $table->columnNames();
```

**Columns**

Load all table columns.

```php
$columns = $table->columns();
```

This method will return a [*Collection*](https://github.com/elusivecodes/FyreCollection) containing the loaded [columns](#columns).

**Foreign Key**

Load a [*ForeignKey*](#foreign-keys).

- `$name` is a string representing the foreign key name.

```php
$foreignKey = $table->foreignKey($name);
```

**Foreign Keys**

Load all table foreign keys.

```php
$foreignKeys = $table->foreignKeys();
```

This method will return a [*Collection*](https://github.com/elusivecodes/FyreCollection) containing the loaded [foreign keys](#foreign-keys).

**Get Comment**

Get the table comment.

```php
$comment = $table->getComment();
```

**Get Name**

Get the table name.

```php
$name = $table->getName();
```

**Get Schema**

Get the [*Schema*](#schemas).

```php
$schema = $table->getSchema();
```

**Has Auto Increment**

Determine whether the table has an auto increment column.

```php
$hasAutoIncrement = $table->hasAutoIncrement();
```

**Has Column**

Determine whether the table has a column.

- `$name` is a string representing the column name.

```php
$hasColumn = $table->hasColumn($name);
```

**Has Foreign Key**

Determine whether the table has a foreign key.

- `$name` is a string representing the foreign key name.

```php
$hasForeignKey = $table->hasForeignKey($name);
```

**Has Index**

Determine whether the table has an index.

- `$name` is a string representing the index name.

```php
$hasIndex = $table->hasIndex($name);
```

**Index**

Load an [*Index*](#indexes).

- `$name` is a string representing the index name.

```php
$index = $table->index($name);
```

**Indexes**

Load all table indexes.

```php
$indexes = $table->indexes();
```

This method will return a [*Collection*](https://github.com/elusivecodes/FyreCollection) containing the loaded [indexes](#indexes).

**Primary Key**

Get the primary key for the table.

```php
$primaryKey = $table->primaryKey();
```

**To Array**

Get the table data as an array.

```php
$data = $table->toArray();
```

### MySQL Tables

**Get Charset**

Get the table character set.

```php
$charset = $table->getCharset();
```

**Get Collation**

Get the table collation.

```php
$collation = $table->getCollation();
```

**Get Engine**

Get the table engine.

```php
$engine = $table->getEngine();
```


## Columns

**Default Value**

Get the evaluated default value for a column.

```php
$defaultValue = $column->defaultValue();
```

**Get Comment**

Get the column comment.

```php
$comment = $column->getComment();
```

**Get Default**

Get the column default value.

```php
$default = $column->getDefault();
```

**Get Length**

Get the column length.

```php
$length = $column->getLength();
```

**Get Name**

Get the column name.

```php
$name = $column->getName();
```

**Get Precision**

Get the column precision.

```php
$precision = $column->getPrecision();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $column->getTable();
```

**Get Type**

Get the column type.

```php
$type = $column->getType();
```

**Is Auto Increment**

Determine whether the column is an auto increment column.

```php
$isAutoIncrement = $column->isAutoIncrement();
```

**Is Nullable**

Determine whether the column is nullable.

```php
$isNullable = $column->isNullable();
```

**Is Unsigned**

Determine whether the column is unsigned.

```php
$isUnsigned = $column->isUnsigned();
```

**To Array**

Get the column data as an array.

```php
$data = $column->toArray();
```

**Type**

Get the type parser for the column.

```php
$typeParser = $column->type();
```

### MySQL Columns

**Get Charset**

Get the column character set.

```php
$charset = $column->getCharset();
```

**Get Collation**

Get the column collation.

```php
$collation = $column->getCollation();
```

**Get Values**

Get the column enum values.

```php
$values = $column->getValues();
```


## Indexes

**Get Columns**

Get the column names.

```php
$columns = $index->getColumns();
```

**Get Name**

Get the index name.

```php
$name = $index->getName();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $index->getTable();
```

**Get Type**

Get the index type.

```php
$type = $index->getType();
```

**Is Primary**

Determine whether the index is primary.

```php
$isPrimary = $index->isPrimary();
```

**Is Unique**

Determine whether the index is unique.

```php
$isUnique = $index->isUnique();
```

**To Array**

Get the index data as an array.

```php
$data = $index->toArray();
```


## Foreign Keys

**Get Columns**

Get the column names.

```php
$columns = $foreignKey->getColumns();
```

**Get Name**

Get the foreign key name.

```php
$name = $foreignKey->getName();
```

**Get On Delete**

Get the delete action.

```php
$onDelete = $foreignKey->getOnDelete();
```

**Get On Update**

Get the update action.

```php
$onUpdate = $foreignKey->getOnUpdate();
```

**Get Referenced Columns**

Get the referenced column names.

```php
$referencedColumn = $foreignKey->getReferencedColumns();
```

**Get Referenced Table**

Get the referenced table name.

```php
$referencedTable = $foreignKey->getReferencedTable();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $foreignKey->getTable();
```

**To Array**

Get the foreign key data as an array.

```php
$data = $foreignKey->toArray();
```