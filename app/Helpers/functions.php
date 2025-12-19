<?php
/**
 * Global helper functions
 */

function e($value): string
{
    if ($value === null) {
        return '';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Format currency
function format_currency($amount) {
    return '₱' . number_format($amount, 2);
}

// Format date
function format_date($date, $format = 'M j, Y g:i A') {
    return date($format, strtotime($date));
}

// Get status badge class
function get_status_badge($status) {
    $classes = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'preparing' => 'bg-indigo-100 text-indigo-800',
        'ready' => 'bg-purple-100 text-purple-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800'
    ];
    
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

// Get payment method icon
function get_payment_icon($method) {
    $icons = [
        'cash' => '💰',
        'gcash' => '📱',
        'card' => '💳',
        'paymaya' => '📲'
    ];
    
    return $icons[$method] ?? '💸';
}

// Get category color
function get_category_color($category_id) {
    $colors = [
        1 => 'bg-red-500',
        2 => 'bg-yellow-500',
        3 => 'bg-green-500',
        4 => 'bg-blue-500',
    ];
    
    return $colors[$category_id] ?? 'bg-gray-500';
}

// Generate URL with base path for subdirectory support
function url($path = '') {
    // Load constants if not already loaded
    if (!defined('BASE_PATH')) {
        require_once dirname(__DIR__, 2) . '/config/constants.php';
    }
    
    // Remove leading slash from path if present
    $path = ltrim($path, '/');
    
    // If path is empty, return base path
    if (empty($path)) {
        return BASE_PATH ?: '/';
    }
    
    // Combine base path with the provided path
    return BASE_PATH . '/' . $path;
}

// Simple redirect helper with base path support
function redirect($path) {
    header("Location: " . url($path));
    exit;
}

// Get stock status badge
function get_stock_badge($stock_quantity, $min_stock) {
    if ($stock_quantity == 0) {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Out of Stock</span>';
    } elseif ($stock_quantity <= $min_stock) {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>';
    } else {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">In Stock</span>';
    }
}

// Get category badge color
function get_category_badge($category_id) {
    $colors = [
        1 => 'bg-red-100 text-red-800',   // Meals
        2 => 'bg-yellow-100 text-yellow-800', // Silogs
        3 => 'bg-green-100 text-green-800',  // Snacks
        4 => 'bg-blue-100 text-blue-800',   // Drinks
    ];
    
    return $colors[$category_id] ?? 'bg-gray-100 text-gray-800';
}

// Format number with K/M suffix
function format_number($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

// Calculate profit margin
function calculate_margin($selling_price, $cost_price) {
    if ($cost_price == 0) return 100;
    $margin = (($selling_price - $cost_price) / $selling_price) * 100;
    return round($margin, 2);
}

// Get margin badge
function get_margin_badge($margin) {
    if ($margin >= 50) {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">High (' . $margin . '%)</span>';
    } elseif ($margin >= 30) {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Good (' . $margin . '%)</span>';
    } elseif ($margin >= 10) {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Fair (' . $margin . '%)</span>';
    } else {
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Low (' . $margin . '%)</span>';
    }
}