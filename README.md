# FyreSchema

**FyreSchema** is a free, database schema library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Schema Registry](#schema-registry)
- [Schemas](#schemas)
- [Table Schemas](#table-schemas)



## Installation

**Using Composer**

```
composer require fyre/schema
```

In PHP:

```php
use Fyre\Schema\SchemaRegistry;
```


## Schema Registry

**Get Cache**

Get the [*Cacher*](https://github.com/elusivecodes/FyreCache#cachers).

```php
$cache = SchemaRegistry::getCache();
```

**Get Schema**

Get the [*Schema*](#schemas) for a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

- `$connection` is a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$schema = SchemaRegistry::getSchema($connection);
```

**Set Cache**

Set the [*Cacher*](https://github.com/elusivecodes/FyreCache#cachers).

- `$cache` is a [*Cacher*](https://github.com/elusivecodes/FyreCache#cachers).

```php
SchemaRegistry::getCache($cache);
```

**Set Handler**

Set a [*Schema*](#schemas) handler for a [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class.

- `$connectionClass` is a string representing the [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class name.
- `$schemaClass` is a string representing the [*Schema*](#schemas) class name.

```php
SchemaRegistry::setHandler($connectionClass, $schemaClass);
```


## Schemas

**Clear**

Clear data from the cache.

```php
$schema->clear();
```

**Describe**

Get the [*TableSchema*](#table-schemas) for a table.

- `$name` is a string representing the table name.

```php
$tableSchema = $schema->describe($name);
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

Determine if the schema has a table.

- `$name` is a string representing the table name.

```php
$hasTable = $schema->hasTable($name);
```

**Table**

Get the data for a table.

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

Get the data for all schema tables.

```php
$tables = $schema->tables();
```


## Table Schemas

**Clear**

Clear data from the cache.

```php
$tableSchema->clear();
```

**Column**

Get the data for a table column.

- `$name` is a string representing the column name.

```php
$column = $tableSchema->column($name);
```

**Column Names**

Get the names of all table columns.

```php
$columnNames = $tableSchema->columnNames();
```

**Columns**

Get the data for all table columns.

```php
$columns = $tableSchema->columns();
```

**Default Value**

Get the default value for a column.

- `$name` is a string representing the column name.

```php
$defaultValue = $tableSchema->defaultValue($name);
```

This method will evaluate expression values (eg. *current_timestamp()*).

**Foreign Key**

Get the data for a table foreign key.

- `$name` is a string representing the foreign key name.

```php
$foreignKey = $tableSchema->foreignKey($name);
```

**Foreign Keys**

Get the data for all table foreign keys.

```php
$foreignKeys = $tableSchema->foreignKeys();
```

**Get Schema**

Get the *Schema*.

```php
$schema = $tableSchema->getSchema();
```

**Get Table Name**

Get the table name.

```php
$tableName = $tableSchema->getTableName();
```

**Get Type**

Get the [*Type*](https://github.com/elusivecodes/FyreTypeParser) parser for a column.

- `$name` is a string representing the column name.

```php
$parser = $tableSchema->getType($name);
```

**Index**

Get the data for a table index.

- `$name` is a string representing the index name.

```php
$index = $tableSchema->index($name);
```

**Indexes**

Get the data for all table indexes.

```php
$indexes = $tableSchema->indexes();
```

**Has Column**

Determine if the table has a column.

- `$name` is a string representing the column name.

```php
$hasColumn = $tableSchema->hasColumn($name);
```

**Has Foreign Key**

Determine if the table has a foreign key.

- `$name` is a string representing the foreign key name.

```php
$hasForeignKey = $tableSchema->hasForeignKey($name);
```

**Has Index**

Determine if the table has an index.

- `$name` is a string representing the index name.

```php
$hasIndex = $tableSchema->hasIndex($name);
```

**Is Nullable**

Determine if a table column is nullable.

- `$name` is a string representing the column name.

```php
$isNullable = $tableSchema->isNullable($name);
```

**Primary Key**

Get the primary key for the table.

```php
$primaryKey = $tableSchema->primaryKey();
```