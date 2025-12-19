<?php
/**
 * User Model - User Management
 */

class User {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        return Database::insert('users', $data);
    }
    
    public function update($id, $data) {
        return Database::update('users', $data, 'id = ?', [$id]);
    }
    
    public function delete($id) {
        return Database::delete('users', 'id = ?', [$id]);
    }
    
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
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
}