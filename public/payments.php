<?php
/**
 * Payments page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new PaymentController();
$controller->index();
