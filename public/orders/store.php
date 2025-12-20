<?php
/**
 * Store order (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new OrderController();
$controller->store();
