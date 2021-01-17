<?php

namespace app\core\database;

/**
 * Class Database
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\Application;

class Database
{
    public $dbh;

    public function __construct($config)
    {
        $dsn = isset($config['dsn']) ? $config['dsn'] : '';
        $user = isset($config['user']) ? $config['user'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        $this->dbh = new \PDO($dsn, $user, $password);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations(); // All applied migrations

        $newMigrations = []; // Array of migrations which can be applied in current run

        $toApplyMigrations = array_diff(scandir(Application::$ROOT_DIR . '/migrations'), $appliedMigrations, ['.', '..']);
        foreach ($toApplyMigrations as $migration) {
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied");
        }
    }

    public function createMigrationsTable()
    {
        $this->dbh->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->dbh->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations($migrations)
    {
        $values = implode(",", array_map(function($value) {
            return "('$value')";
        }, $migrations));
        $sql = "INSERT INTO migrations (migration) VALUES $values";
        $statement = $this->dbh->prepare($sql);
        $statement->execute();
    }

    public function log($message)
    {
        echo '[' . Date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}