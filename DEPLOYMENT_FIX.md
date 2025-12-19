# Subdirectory Deployment Fix

## Root Cause Summary

The application was hardcoding absolute URLs starting with `/` (e.g., `/dashboard`, `/login`) in all redirects, links, and form actions. This works fine when the application is deployed at the document root (e.g., `http://localhost/`), but fails when deployed in a subdirectory like `htdocs/bussin_foodie/public` under XAMPP. In subdirectory deployments, redirects would incorrectly point to `http://localhost/login` instead of `http://localhost/bussin_foodie/public/index.php/login`, resulting in 404 errors.

## Solution

Added automatic base path detection and a helper function to generate correct URLs:

1. **BASE_PATH constant**: Automatically detects the application's base path from `$_SERVER['SCRIPT_NAME']`
2. **url() helper function**: Prepends the base path to all relative URLs
3. **Updated all redirects and links**: Modified controllers, views, and helpers to use the new url() function

## Changes Made

### Files Modified

1. **config/constants.php** - Added BASE_PATH constant with auto-detection
2. **app/Helpers/functions.php** - Added url() helper and updated redirect() function
3. **public/index.php** - Load constants.php early to ensure BASE_PATH is available
4. **app/Helpers/Auth.php** - Updated redirectToLogin() to use url() helper
5. **app/Controllers/AuthController.php** - Updated all redirects and form action
6. **All other controllers** (8 files) - Updated redirect() methods to use url() helper
7. **All view files** (19 files) - Updated href and action attributes to use url() helper

### Code Changes Explained

#### 1. BASE_PATH Constant (config/constants.php)
```php
// Auto-detect base path from the script location
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
define('BASE_PATH', $basePath);
```
- Extracts the directory path from SCRIPT_NAME
- Handles root deployment (returns empty string) and subdirectory deployment

#### 2. url() Helper Function (app/Helpers/functions.php)
```php
function url($path = '') {
    if (!defined('BASE_PATH')) {
        require_once dirname(__DIR__, 2) . '/config/constants.php';
    }
    $path = ltrim($path, '/');
    if (empty($path)) {
        return BASE_PATH ?: '/';
    }
    return BASE_PATH . '/' . $path;
}
```
- Ensures constants are loaded
- Removes leading slashes from paths
- Combines BASE_PATH with the provided path

#### 3. Updated redirect() Function
```php
function redirect($path) {
    header("Location: " . url($path));
    exit;
}
```
- Now uses url() helper instead of raw path

#### 4. Controller Updates
All controller redirect methods changed from:
```php
private function redirect($url) {
    header('Location: ' . $url);
    exit;
}
```
To:
```php
private function redirect($path) {
    header('Location: ' . url($path));
    exit;
}
```

All redirect calls changed from `/path` to `path`:
```php
// Before:
$this->redirect('/dashboard');

// After:
$this->redirect('dashboard');
```

#### 5. View Updates
All links and forms changed from hardcoded paths to url() helper:
```php
// Before:
<a href="/products">Products</a>
<form action="/login" method="POST">

// After:
<a href="<?= url('products') ?>">Products</a>
<form action="<?= url('login') ?>" method="POST">
```

## Manual Test Checklist

### Setup
1. Copy the application to `htdocs/bussin_foodie/`
2. Ensure database is configured in `config/database.php`
3. Access via `http://localhost/bussin_foodie/public/index.php`

### Test Cases

- [ ] **Login Page Loads**
  - Navigate to `http://localhost/bussin_foodie/public/index.php`
  - Verify login form appears
  - Check that form action points to correct path (inspect element)

- [ ] **Login Redirect Works**
  - Enter credentials (default: admin / admin123)
  - Click "Sign In"
  - Verify redirect goes to `http://localhost/bussin_foodie/public/index.php/dashboard`
  - **NOT** to `http://localhost/dashboard` (404)
  - Verify dashboard loads successfully

- [ ] **Navigation Links Work**
  - Click "Products" in sidebar
  - Verify URL is `http://localhost/bussin_foodie/public/index.php/products`
  - Click "Orders" in sidebar
  - Verify URL is `http://localhost/bussin_foodie/public/index.php/orders`
  - Click "Dashboard" in sidebar
  - Verify URL is `http://localhost/bussin_foodie/public/index.php/dashboard`

- [ ] **Form Submissions Work**
  - Navigate to Products → Create New Product
  - Verify URL includes project path
  - Submit a test product
  - Verify redirect back to products list includes project path
  - No 404 errors occur

- [ ] **Logout Works**
  - Click logout button
  - Verify redirect to `http://localhost/bussin_foodie/public/index.php/login`
  - Verify login page loads correctly

- [ ] **Unauthorized Access Protection**
  - Logout if logged in
  - Try accessing `http://localhost/bussin_foodie/public/index.php/dashboard` directly
  - Verify redirect to login page
  - Verify login page URL includes project path

### Additional Verification

- [ ] Check browser console for any 404 errors
- [ ] Verify all CSS and JS assets load correctly
- [ ] Test in different browsers (Chrome, Firefox, Edge)
- [ ] Verify no broken links in navigation

## Optional: Automated Test (cURL)

Save as `test_redirects.sh`:

```bash
#!/bin/bash

BASE_URL="http://localhost/bussin_foodie/public/index.php"

echo "Testing Bussin' Foodie Redirects..."
echo "===================================="
echo ""

# Test 1: Login page loads
echo "Test 1: Login page loads"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL")
if [ "$RESPONSE" = "200" ]; then
    echo "✓ PASS: Login page returns 200"
else
    echo "✗ FAIL: Login page returns $RESPONSE"
fi
echo ""

# Test 2: Dashboard requires authentication (redirects)
echo "Test 2: Dashboard redirects to login when not authenticated"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" -L "$BASE_URL/dashboard")
if [ "$RESPONSE" = "200" ]; then
    echo "✓ PASS: Dashboard redirect works (ends at login)"
else
    echo "✗ FAIL: Dashboard returns $RESPONSE"
fi
echo ""

# Test 3: Check redirect location header
echo "Test 3: Verify redirect location includes base path"
LOCATION=$(curl -s -I "$BASE_URL/dashboard" | grep -i "^Location:" | cut -d' ' -f2 | tr -d '\r\n')
if [[ "$LOCATION" == *"bussin_foodie"* ]]; then
    echo "✓ PASS: Redirect location includes project path: $LOCATION"
else
    echo "✗ FAIL: Redirect location missing project path: $LOCATION"
fi
echo ""

echo "===================================="
echo "Test complete!"
```

Run with: `bash test_redirects.sh`

## Deployment Notes

### For XAMPP (Windows/Mac/Linux)
1. Place project in `htdocs/bussin_foodie/`
2. Access via `http://localhost/bussin_foodie/public/index.php`
3. No configuration changes needed - BASE_PATH auto-detects!

### For Root Deployment
1. Place public folder contents in document root
2. Move other files outside document root
3. Update paths in index.php if needed
4. BASE_PATH will be empty string automatically

### For Production with Virtual Host
1. Set DocumentRoot to the `public` folder
2. BASE_PATH will be empty string automatically
3. .htaccess handles clean URLs

## Benefits of This Approach

1. **Zero Configuration**: BASE_PATH auto-detects from server environment
2. **Portable**: Works in any subdirectory without changes
3. **Maintainable**: Single helper function for all URL generation
4. **Safe**: All redirects now consistent and correct
5. **No Breaking Changes**: Works in both root and subdirectory deployments

## Troubleshooting

If redirects still fail:

1. **Verify .htaccess is active**:
   ```bash
   # Check Apache has mod_rewrite enabled
   # In httpd.conf, ensure:
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

2. **Check AllowOverride**:
   ```apache
   <Directory "C:/xampp/htdocs">
       AllowOverride All
   </Directory>
   ```

3. **Clear browser cache**: Old cached redirects can cause issues

4. **Check PHP error logs**: Look for any issues loading constants.php

5. **Verify BASE_PATH value**:
   ```php
   // Add to index.php temporarily:
   echo "BASE_PATH: " . BASE_PATH;
   exit;
   ```
