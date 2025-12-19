<?php
/**
 * Front controller for Bussin' Foodie
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Definitions
define("ROOT_PATH", dirname(__DIR__));
define("PUBLIC_PATH", __DIR__);
define("APP_PATH", ROOT_PATH . "/app");
define("VIEWS_PATH", APP_PATH . "/Views");

// Load constants (includes BASE_PATH)
require ROOT_PATH . "/config/constants.php";

require "../app/Helpers/functions.php";

// Router function
function route($uri)
{
  // 1. Get the path of the current script
  $scriptName = $_SERVER["SCRIPT_NAME"];
  $scriptDir = dirname($scriptName);

  // 2. Parse the requested URI
  $requestUri = parse_url($uri, PHP_URL_PATH);

  // 3. Remove the script directory from the request URI to get the relative route
  if (strpos($requestUri, $scriptDir) === 0) {
    $uri = substr($requestUri, strlen($scriptDir));
  } elseif (strpos($requestUri, dirname($scriptDir)) === 0) {
    $uri = substr($requestUri, strlen(dirname($scriptDir)));
  } else {
    $uri = $requestUri;
  }

  // 4. Clean up the URI
  $uri = trim($uri, "/");

  // Remove 'index.php' if it's part of the URL
  if (strpos($uri, "index.php") === 0) {
    $uri = substr($uri, 9);
    $uri = trim($uri, "/");
  }

  // --- CHANGE HERE: Default route points to LOGIN now ---
  if (empty($uri)) {
    return ["controller" => "AuthController", "method" => "login"];
  }

  // Define Routes
  $routes = [
    // Admin / System Routes
    "login" => ["controller" => "AuthController", "method" => "login"],
    "logout" => ["controller" => "AuthController", "method" => "logout"],
    "dashboard" => ["controller" => "DashboardController", "method" => "index"],

    // Products
    "products" => ["controller" => "ProductController", "method" => "index"],
    "products/create" => [
      "controller" => "ProductController",
      "method" => "create",
    ],
    "products/store" => [
      "controller" => "ProductController",
      "method" => "store",
    ],
    "products/edit/(\d+)" => [
      "controller" => "ProductController",
      "method" => "edit",
    ],
    "products/update/(\d+)" => [
      "controller" => "ProductController",
      "method" => "update",
    ],
    "products/delete/(\d+)" => [
      "controller" => "ProductController",
      "method" => "delete",
    ],
    "products/update-stock/(\d+)" => [
      "controller" => "StockController",
      "method" => "update",
    ],

    // Orders
    "orders" => ["controller" => "OrderController", "method" => "index"],
    "orders/create" => [
      "controller" => "OrderController",
      "method" => "create",
    ],
    "orders/store" => ["controller" => "OrderController", "method" => "store"],
    "orders/view/(\d+)" => [
      "controller" => "OrderController",
      "method" => "view",
    ],
    "orders/edit/(\d+)" => [
      "controller" => "OrderController",
      "method" => "edit",
    ],
    "orders/update/(\d+)" => [
      "controller" => "OrderController",
      "method" => "update",
    ],
    "orders/delete/(\d+)" => [
      "controller" => "OrderController",
      "method" => "delete",
    ],
    "orders/update-status" => [
      "controller" => "OrderController",
      "method" => "updateStatus",
    ],

    // Customers
    "customers" => ["controller" => "CustomerController", "method" => "index"],
    "customers/create" => [
      "controller" => "CustomerController",
      "method" => "create",
    ],
    "customers/store" => [
      "controller" => "CustomerController",
      "method" => "store",
    ],
    "customers/edit/(\d+)" => [
      "controller" => "CustomerController",
      "method" => "edit",
    ],
    "customers/update/(\d+)" => [
      "controller" => "CustomerController",
      "method" => "update",
    ],
    "customers/delete/(\d+)" => [
      "controller" => "CustomerController",
      "method" => "delete",
    ],

    // Users
    "users" => ["controller" => "UserController", "method" => "index"],
    "users/create" => ["controller" => "UserController", "method" => "create"],
    "users/store" => ["controller" => "UserController", "method" => "store"],
    "users/edit/(\d+)" => [
      "controller" => "UserController",
      "method" => "edit",
    ],
    "users/update/(\d+)" => [
      "controller" => "UserController",
      "method" => "update",
    ],
    "users/delete/(\d+)" => [
      "controller" => "UserController",
      "method" => "delete",
    ],

    // Settings & Payments
    "settings" => ["controller" => "SettingController", "method" => "index"],
    "settings/update" => [
      "controller" => "SettingController",
      "method" => "update",
    ],
    "payments" => ["controller" => "PaymentController", "method" => "index"],
    "payments/record/(\d+)" => [
      "controller" => "PaymentController",
      "method" => "recordPayment",
    ],

  ];

  // Match Routes
  foreach ($routes as $route => $handler) {
    $pattern = "#^" . preg_replace("#\(\\\\d\+\)#", "(\d+)", $route) . '$#';
    if (preg_match($pattern, $uri, $matches)) {
      array_shift($matches);
      return [
        "controller" => $handler["controller"],
        "method" => $handler["method"],
        "params" => $matches,
      ];
    }
  }

  return null;
}

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

// Dispatch
$requestUri = $_SERVER["REQUEST_URI"];
$route = route($requestUri);

if ($route) {
  $controllerName = $route["controller"];
  $methodName = $route["method"];
  $params = $route["params"] ?? [];

  if (file_exists(APP_PATH . "/Controllers/" . $controllerName . ".php")) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
      call_user_func_array([$controller, $methodName], $params);
    } else {
      http_response_code(404);
      echo "Method not found";
    }
  } else {
    http_response_code(404);
    echo "Controller not found: " . $controllerName;
  }
} else {
  http_response_code(404);
  echo "404 - Page Not Found";
}
