<?php
/**
 * Logout page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new AuthController();
$controller->logout();
