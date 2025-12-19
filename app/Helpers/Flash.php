<?php
/**
 * Flash messaging helper
 */

class Flash {
    public static function set($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }
    
    public static function get($type) {
        if (isset($_SESSION['flash_' . $type])) {
            $message = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
            return $message;
        }
        return null;
    }
    
    public static function has($type) {
        return isset($_SESSION['flash_' . $type]);
    }
    
    public static function success($message) {
        self::set('success', $message);
    }
    
    public static function error($message) {
        self::set('error', $message);
    }
    
    public static function warning($message) {
        self::set('warning', $message);
    }
    
    public static function info($message) {
        self::set('info', $message);
    }
}