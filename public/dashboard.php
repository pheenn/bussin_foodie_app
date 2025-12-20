<?php
/**
 * Dashboard page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new DashboardController();
$controller->index();
