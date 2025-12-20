<?php
/**
 * Create product page
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new ProductController();
$controller->create();
