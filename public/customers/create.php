<?php
/**
 * Create customer page
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new CustomerController();
$controller->create();
