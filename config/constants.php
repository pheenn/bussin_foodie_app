<?php
/**
 * Application constants for Bussin' Foodie
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Bussin\' Foodie');
    define('APP_VERSION', '1.0.0');
    
    // Session settings
    define('SESSION_TIMEOUT', 3600);
    
    // File upload settings
    define('MAX_FILE_SIZE', 5242880);
    define('UPLOAD_PATH', dirname(__DIR__) . '/storage/uploads/');
    
    // Pagination
    define('ITEMS_PER_PAGE', 20);
    
    // Currency
    define('CURRENCY_SYMBOL', '₱');
    define('CURRENCY_CODE', 'PHP');
}