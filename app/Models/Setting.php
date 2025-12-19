<?php
/**
 * Setting Model - System Configuration
 */

class Setting {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    /**
     * Get all settings as an associative array (key => value)
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM system_settings");
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }
    
    /**
     * Update a specific setting
     */
    public function update($key, $value) {
        // Check if key exists
        $stmt = $this->db->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        
        if ($stmt->fetch()) {
            // Update
            return Database::update('system_settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            // Insert (if new setting)
            return Database::insert('system_settings', [
                'setting_key' => $key, 
                'setting_value' => $value
            ]);
        }
    }
    
    /**
     * Bulk update settings
     */
    public function updateBatch($data) {
        $count = 0;
        foreach ($data as $key => $value) {
            $this->update($key, $value);
            $count++;
        }
        return $count;
    }
}