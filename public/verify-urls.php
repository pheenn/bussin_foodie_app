<?php
/**
 * Verification script - Test URL generation
 */

require_once __DIR__ . '/bootstrap.php';

echo "Testing URL Generation\n";
echo "======================\n\n";

// Test cases
$tests = [
    ['input' => '', 'expected_contains' => 'dashboard.php'],
    ['input' => 'login', 'expected_contains' => 'login.php'],
    ['input' => 'products', 'expected_contains' => 'products.php'],
    ['input' => 'products/create', 'expected_contains' => 'products/create.php'],
    ['input' => 'products/edit/5', 'expected_contains' => 'products/edit.php?id=5'],
    ['input' => 'orders/view/10', 'expected_contains' => 'orders/view.php?id=10'],
    ['input' => 'users/edit/3', 'expected_contains' => 'users/edit.php?id=3'],
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    $result = url($test['input']);
    $contains = strpos($result, $test['expected_contains']) !== false;
    
    if ($contains) {
        echo "✓ PASS: url('{$test['input']}') => {$result}\n";
        $passed++;
    } else {
        echo "✗ FAIL: url('{$test['input']}') => {$result}\n";
        echo "  Expected to contain: {$test['expected_contains']}\n";
        $failed++;
    }
}

echo "\n======================\n";
echo "Results: {$passed} passed, {$failed} failed\n";

if ($failed === 0) {
    echo "\n✓ All tests passed!\n";
    exit(0);
} else {
    echo "\n✗ Some tests failed!\n";
    exit(1);
}
