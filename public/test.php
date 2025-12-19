<?php
// Simple test script
echo "<h1>Bussin' Foodie Test Page</h1>";

// Test database connection
try {
    $host = '127.0.0.1';
    $dbname = 'bussin_foodie';
    $username = 'root';
    $password = '10282001birthday';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>✓ Database connection successful!</p>";
  
  echo password_hash('admin123', PASSWORD_DEFAULT);
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "<p>Products in database: " . $result['count'] . "</p>";
    
    // Test users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $result = $stmt->fetch();
    echo "<p>Admin users: " . $result['count'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
UPDATE users
SET password_hash = '$2y$12$Xv2gEy1aZooGHketQ.JO3e4SrEoqiEOd0Pl8NRSuZnANRXia2EeX'
WHERE role = 'admin';

// Test session
session_start();
echo "<p>Session ID: " . session_id() . "</p>";

// Test PHP version
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test links
echo '<h2>Test Links:</h2>';
echo '<ul>';
echo '<li><a href="/">Home/Dashboard</a></li>';
echo '<li><a href="/login">Login Page</a></li>';
echo '<li><a href="/dashboard">Dashboard</a></li>';
echo '<li><a href="/products">Products</a></li>';
echo '</ul>';