<?php
/**
 * Update order status (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new OrderController();
$controller->updateStatus();
