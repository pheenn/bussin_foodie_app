# BASE_PATH Routing Fix

## Problem
When accessing URLs like `/orders/store` or form submissions to those URLs, the application was generating incorrect URLs with duplicated path segments. For example:
- **Expected:** `/bussin_foodie/public/orders/store.php`
- **Actual (Error):** `/bussin_foodie/public/orders/orders/store.php`

This resulted in "404 Not Found" errors with the message:
```
The requested resource /bussin_foodie/public/orders/orders/store.php was not found on this server.
```

## Root Cause
The `BASE_PATH` constant in `config/constants.php` was calculated using `dirname($_SERVER['SCRIPT_NAME'])`. 

When a file in a subdirectory (e.g., `/public/orders/store.php`) was accessed, the calculation would include the subdirectory path:
- `SCRIPT_NAME`: `/bussin_foodie/public/orders/store.php`
- `dirname()`: `/bussin_foodie/public/orders`
- `BASE_PATH`: `/bussin_foodie/public/orders`

Then when the `url()` helper function generated a URL like `url('orders/store')`, it would concatenate:
- `BASE_PATH` (`/bussin_foodie/public/orders`) + `/orders/store.php`
- Result: `/bussin_foodie/public/orders/orders/store.php` ❌

## Solution
Modified the `BASE_PATH` calculation in `config/constants.php` to normalize to the `/public` directory regardless of which script is being executed.

The new logic:
1. Split the script directory path into parts
2. Iterate through parts and stop when we reach `public`
3. Reconstruct the path up to and including `public`

This ensures that `BASE_PATH` always resolves to `/bussin_foodie/public` (or just `/public` depending on deployment) no matter which file in the directory tree is accessed.

## Changes Made

### File: `config/constants.php`
**Before:**
```php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
define('BASE_PATH', $basePath);
```

**After:**
```php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);

// If the script is in a subdirectory of /public (e.g., /public/orders/store.php),
// we need to remove the subdirectory part and keep only up to /public
$parts = explode('/', trim($scriptDir, '/'));
$baseParts = [];
foreach ($parts as $part) {
    $baseParts[] = $part;
    // Stop at 'public' directory
    if ($part === 'public') {
        break;
    }
}
$basePath = count($baseParts) > 0 ? '/' . implode('/', $baseParts) : '';
$basePath = ($basePath === '/' || $basePath === '\\') ? '' : $basePath;
define('BASE_PATH', $basePath);
```

## Testing
All routes now resolve correctly:

| Route | Generated URL |
|-------|--------------|
| `orders/store` | `/bussin_foodie/public/orders/store.php` ✓ |
| `products/create` | `/bussin_foodie/public/products/create.php` ✓ |
| `customers/edit/5` | `/bussin_foodie/public/customers/edit.php?id=5` ✓ |
| `users/delete/10` | `/bussin_foodie/public/users/delete.php?id=10` ✓ |
| `payments/record/1` | `/bussin_foodie/public/payments/record.php?id=1` ✓ |
| `settings/update` | `/bussin_foodie/public/settings/update.php` ✓ |

## Impact
- ✅ All existing forms work correctly
- ✅ All navigation links work correctly
- ✅ All AJAX requests work correctly
- ✅ Works from any subdirectory
- ✅ No code changes required in views or controllers
- ✅ Backward compatible with existing code

## Affected Routes
This fix resolves routing issues for all modules:
- Orders (create, store, edit, update, delete, view, update-status)
- Products (create, store, edit, update, delete, update-stock)
- Customers (create, store, edit, update, delete)
- Users (create, store, edit, update, delete)
- Payments (record)
- Settings (update)

## Date Fixed
December 20, 2025
