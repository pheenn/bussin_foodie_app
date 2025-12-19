<?php
// Script to fix admin password
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check current password
    $stmt = $pdo->prepare("SELECT username, password_hash FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "Current admin password hash: " . $user['password_hash'] . "\n";
    
    // Create new password hash for 'admin123'
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "New hash for 'admin123': " . $newHash . "\n";
    
    // Update password
    $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
    $updateStmt->execute([$newHash]);
    
    echo "Password updated successfully!\n";
    
    // Test the new password
    $testPassword = 'admin123';
    if (password_verify($testPassword, $newHash)) {
        echo "Password verification successful!\n";
    } else {
        echo "Password verification failed!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}