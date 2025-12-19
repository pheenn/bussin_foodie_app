<?php
/**
 * Settings page
 */

require_once __DIR__ . '/bootstrap.php';

// Handle request
$controller = new SettingController();
$controller->index();
