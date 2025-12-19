<?php
/**
 * Record payment (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Get order ID from query string
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ' . url('payments.php'));
    exit;
}

// Handle request
$controller = new PaymentController();
$controller->recordPayment($id);
