<?php
/**
 * Create order page
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new OrderController();
$controller->create();
