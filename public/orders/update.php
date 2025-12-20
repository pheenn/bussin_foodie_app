<?php
/**
 * Update order (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Get order ID from query string
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . url('orders.php'));
    exit;
}

// Handle request
$controller = new OrderController();
$controller->update($id);
