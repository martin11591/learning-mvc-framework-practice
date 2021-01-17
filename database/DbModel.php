<?php

namespace app\core\database;

/**
 * Class DbModel
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

use app\core\Application;
use app\core\Model;

abstract class DbModel extends Model
{
    public static function tableName()
    {
        return '';
    }

    public static function primaryKey()
    {
        return '';
    }

    abstract public function attributes();

    public function save()
    {
        $tableName = static::tableName();
        $attributes = $this->attributes();
        $sql = "INSERT INTO $tableName (" . implode(',', $attributes) . ") VALUES (" . self::prepareToBind($attributes) . ")";

        try {
            $statement = self::prepare($sql);
            foreach ($attributes as $attribute) {
                $statement->bindValue(":$attribute", $this->{$attribute});
            }

            $statement->execute();
        } catch (\PDOException $error) {
            Application::$app->session->setFlash('db', 'Cannot save: ' . $error->getMessage());
            return false;
        }

        return true;
    }

    public static function findOne($search)
    {
        $tableName = static::tableName();
        $attributes = array_keys($search);
        $sql = "SELECT * FROM $tableName WHERE " . self::prepareToBind($attributes, "AND ");
        $statement = self::prepare($sql);
        foreach ($search as $attribute => $value) {
            $statement->bindValue(":$attribute", $value);
        }
        $statement->execute();

        return $statement->fetchObject(static::class);
    }

    public static function prepare($sql)
    {
        return Application::$app->db->dbh->prepare($sql);
    }

    public static function prepareToBind($attributes, $glue = ",")
    {
        $params = array_map(function($attr)
        {
            return "$attr = :$attr";
        }, $attributes);
        return implode($glue, $params);
    }
}