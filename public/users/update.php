<?php
/**
 * Update user (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Get user ID from query string
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . url('users.php'));
    exit;
}

// Handle request
$controller = new UserController();
$controller->update($id);
