<?php
/**
 * Database configuration for Bussin' Foodie
 */


if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'bussin_foodie');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
    
    // PDO options
    define('DB_OPTIONS', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

// Create database connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        
        // Test connection
        $pdo->query("SELECT 1");
        error_log("Database connection successful to " . DB_NAME);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection error. Please check configuration.");
    }
}