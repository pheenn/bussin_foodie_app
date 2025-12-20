<?php
/**
 * Store customer (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new CustomerController();
$controller->store();
