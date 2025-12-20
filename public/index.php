<?php
/**
 * Front controller for Bussin' Foodie - redirects to login or dashboard
 */

require_once __DIR__ . '/bootstrap.php';

// Check if user is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
