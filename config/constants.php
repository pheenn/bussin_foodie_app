<?php
/**
 * Application constants for Bussin' Foodie
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Bussin\' Foodie');
    define('APP_VERSION', '1.0.0');
    
    // Base path for URL generation (auto-detect from SCRIPT_NAME)
    // This handles subdirectory deployments like /bussin_foodie/public/
    // Always normalize to the /public directory to avoid duplication in subdirectories
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $scriptDir = dirname($scriptName);
    
    // If the script is in a subdirectory of /public (e.g., /public/orders/store.php),
    // we need to remove the subdirectory part and keep only up to /public
    $parts = explode('/', trim($scriptDir, '/'));
    $baseParts = [];
    $foundPublic = false;
    foreach ($parts as $part) {
        $baseParts[] = $part;
        // Stop at 'public' directory
        if ($part === 'public') {
            $foundPublic = true;
            break;
        }
    }
    
    // If 'public' was not found in the path, use the full script directory as fallback
    if (!$foundPublic) {
        $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
    } else {
        $basePath = count($baseParts) > 0 ? '/' . implode('/', $baseParts) : '';
        $basePath = ($basePath === '/' || $basePath === '\\') ? '' : $basePath;
    }
    define('BASE_PATH', $basePath);
    
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