<?php
// /classes/Helper/DB.php

class DB
{
    private $host;
    private $user;
    private $password;
    private $database;

    private $connexion;

    private static $instance;

    private function __construct()
    {
        $this->host = 'localhost';
        $this->user = 'patricnumeric';
        $this->password = 'superpassword';
        $this->database = 'pratiquesnumeriques';

        $this->connexion = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->password);
    }

    public static function get_instance() {
        if(!isset(self::$instance)) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public static function select(string $table, array $attributes = [], string $fields = '*') {
        $sql = "SELECT {$fields} FROM {$table} WHERE 1=1";
        foreach($attributes as $key => $value) {
            $sql .= " AND {$key} = :{$key}";
        }
        return self::select_sql($sql, $attributes);

    }

    public static function select_sql(string $sql, array $attributes = []) {
        $cursor_attributes = [];
        foreach($attributes as $key => $value) {
            $cursor_attributes[':' . $key] = $value;
        }

        $query = self::get_instance()->connexion->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $query->execute($cursor_attributes);
        $values = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        $tab = [];
        foreach($values as $key => $value) {
            $tab[$key] = (object)$value;
        }

        return $tab;
    }

    public static function insert(string $table, array $attributes) {
        $cursor_attributes = [];
        foreach($attributes as $key => $value) {
            $cursor_attributes[':' . $key] = $value;
        }

        $sql = "INSERT INTO {$table} (" . implode(', ', array_keys($attributes)) . ") VALUES (" . implode(', ', array_keys($cursor_attributes)) . ')';
        $query = self::get_instance()->connexion->prepare($sql);
        if($query->execute($attributes)) {
            return (int)self::get_instance()->connexion->lastInsertId();
        } else {
            return false;
        }
    }

    public static function update(string $table, array $attributes, array $where) {
        if(count($attributes) === 0) {
            throw new Exception("Nothing to update");
        }

        $update = [];
        foreach($attributes as $key => $value) {
            $update[] = "$key = :{$key}";
        }
        $update_sql = implode(', ', $update);

        $where_sql = '';
        foreach($where as $key => $value) {
            $where_sql .= " AND {$key} = :{$key}";
        }

        $params = $attributes + $where;
        $sql = "UPDATE {$table} SET {$update_sql} WHERE 1=1 $where_sql";

        $query = self::get_instance()->connexion->prepare($sql);
        return $query->execute($params);
    }

    public static function delete(string $table, $where)  {
        $where_sql = '';
        foreach($where as $key => $value) {
            $where_sql .= " AND {$key} = :{$key}";
        }
        $sql = "DELETE from {$table} WHERE 1=1 $where_sql";

        $query = self::get_instance()->connexion->prepare($sql);
        return $query->execute($where);
    }
}