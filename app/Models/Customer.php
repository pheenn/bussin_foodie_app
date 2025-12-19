<?php
/**
 * Customer Model - Customer Management
 */

class Customer {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    /**
     * Retrieve all customers from the database.
     */
    public function getAll() {
        // We select the most relevant fields for the index view
        $stmt = $this->db->query("SELECT id, name, email, phone, address, created_at FROM customers ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Find a customer by ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Insert a new customer record.
     */
    public function create($data) {
        return Database::insert('customers', $data);
    }
    
    /**
     * Update an existing customer record.
     */
    public function update($id, $data) {
        return Database::update('customers', $data, 'id = ?', [$id]);
    }
    
    /**
     * Delete a customer record.
     */
    public function delete($id) {
        return Database::delete('customers', 'id = ?', [$id]);
    }
    
    /**
     * Check if an email already exists (excluding the current customer ID).
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM customers WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Check if a phone number already exists (excluding the current customer ID).
     */
    public function phoneExists($phone, $excludeId = null) {
        // Assuming phone number is stored without non-numeric characters for comparison
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $sql = "SELECT COUNT(*) as count FROM customers WHERE REPLACE(phone, '-', '') = ?";
        $params = [$phone];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}