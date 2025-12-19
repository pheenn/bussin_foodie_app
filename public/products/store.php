<?php
/**
 * Store product (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new ProductController();
$controller->store();
