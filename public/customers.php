<?php
/**
 * Customers list page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new CustomerController();
$controller->index();
