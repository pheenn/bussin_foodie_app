<?php
/**
 * Update product stock (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Get product ID from query string
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . url('products.php'));
    exit;
}

// Handle request
$controller = new StockController();
$controller->update($id);
