<?php
/**
 * Users list page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new UserController();
$controller->index();
