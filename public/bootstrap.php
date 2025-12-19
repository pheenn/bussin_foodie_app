<?php
/**
 * Bootstrap file - Common initialization for all pages
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Definitions
if (!defined("ROOT_PATH")) {
    define("ROOT_PATH", dirname(__DIR__));
}
if (!defined("PUBLIC_PATH")) {
    define("PUBLIC_PATH", __DIR__);
}
if (!defined("APP_PATH")) {
    define("APP_PATH", ROOT_PATH . "/app");
}
if (!defined("VIEWS_PATH")) {
    define("VIEWS_PATH", APP_PATH . "/Views");
}

// Load constants
require_once ROOT_PATH . "/config/constants.php";
require_once APP_PATH . "/Helpers/functions.php";

// Autoload
spl_autoload_register(function ($className) {
    $paths = [
        APP_PATH . "/Controllers/",
        APP_PATH . "/Models/",
        APP_PATH . "/Helpers/",
    ];
    foreach ($paths as $path) {
        if (file_exists($path . $className . ".php")) {
            require_once $path . $className . ".php";
            return;
        }
    }
});
