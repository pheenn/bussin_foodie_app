<?php
/**
 * Create user page
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new UserController();
$controller->create();
