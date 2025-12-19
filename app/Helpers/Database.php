<?php
/**
 * Database helper class with PDO wrapper
 */

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                require_once dirname(__DIR__, 2) . '/config/database.php';
                self::$connection = getDBConnection();
            } catch (Exception $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw $e;
            }
        }
        return self::$connection;
    }
    
    public static function query($sql, $params = []) {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            throw $e;
        }
    }
    
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function fetchOne($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }
    
    public static function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($data);
        
        return self::getConnection()->lastInsertId();
    }
    
    public static function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        $values = []; 
        

        foreach ($data as $column => $value) {
            $setParts[] = "$column = ?";
            $values[] = $value;
        }
        
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        
        $allParams = array_merge($values, $whereParams);
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($allParams);
        
        return $stmt->rowCount();
    }
    
    public static function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}