<?php
/**
 * Orders list page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new OrderController();
$controller->index();
