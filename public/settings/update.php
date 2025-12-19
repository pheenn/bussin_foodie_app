<?php
/**
 * Update settings (form handler)
 */

require_once dirname(__DIR__) . '/bootstrap.php';

// Handle request
$controller = new SettingController();
$controller->update();
