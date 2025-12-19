<?php
/**
 * Delete customer (action handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Get customer ID from query string
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . url('customers.php'));
    exit;
}

// Handle request
$controller = new CustomerController();
$controller->delete($id);
