<?php
/**
 * Store user (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new UserController();
$controller->store();
