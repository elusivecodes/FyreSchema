<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Cache\Cache,
    Fyre\Cache\Handlers\FileCacher,
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    Fyre\DB\Handlers\MySQL\MySQLConnection,
    Fyre\FileSystem\Folder,
    Fyre\Schema\Schema,
    Fyre\Schema\SchemaRegistry;

use function
    getenv;

trait ConnectionTrait
{

    protected Connection $db;

    protected Schema $schema;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
        $this->schema = SchemaRegistry::getSchema($this->db);
    }

    protected function tearDown(): void
    {
        $folder = new Folder('tmp');

        if ($folder->exists()) {
            $folder->delete();
        }
    }

    public static function setUpBeforeClass(): void
    {
        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class,
            'host' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
            'collation' => 'utf8mb4_unicode_ci',
            'charset' => 'utf8mb4',
            'compress' => true,
            'persist' => true
        ]);

        Cache::setConfig('schema', [
            'className' =>  FileCacher::class,
            'path' => 'tmp',
            'prefix' => 'schema.',
            'expire' => 3600
        ]);

        $cache = Cache::use('schema');

        SchemaRegistry::setCache($cache);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS `test_values`');
        $connection->query('DROP TABLE IF EXISTS `test`');

        $connection->query(<<<EOT
            CREATE TABLE `test` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                `value` INT(10) UNSIGNED NOT NULL DEFAULT '5',
                `price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '2.50',
                `text` VARCHAR(255) NOT NULL DEFAULT 'default' COLLATE 'utf8mb4_unicode_ci',
                `test` ENUM('Y','N') NOT NULL DEFAULT 'Y',
                `created` DATETIME NOT NULL DEFAULT current_timestamp(),
                `modified` DATETIME NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE INDEX `name` (`name`),
                INDEX `name_value` (`name`, `value`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
        $connection->query(<<<EOT
            CREATE TABLE `test_values` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `test_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
                `value` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `test_values_test_id` (`test_id`),
                INDEX `value` (`value`),
                CONSTRAINT `test_values_test_id` FOREIGN KEY (`test_id`) REFERENCES `test`.`test` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
    }

    public static function tearDownAfterClass(): void
    {
        $connection = ConnectionManager::use();
        $connection->query('DROP TABLE IF EXISTS `test_values`');
        $connection->query('DROP TABLE IF EXISTS `test`');
    }

}
