<?php
/**
 * Products list page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new ProductController();
$controller->index();
