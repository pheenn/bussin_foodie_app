<?php
/**
 * Payment Model - Handles payment transactions
 */

class Payment {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    /**
     * Get all payments with associated order details
     */
    public function getAll($filters = []) {
            $sql = "
            SELECT p.*, o.customer_name, o.total_amount AS order_total
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            ORDER BY p.created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get payments for a specific order
     */
    public function getByOrderId($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY created_at ASC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Record a new payment (for an existing order)
     * Data array should include: order_id, amount, payment_method, (optional) transaction_id
     */
    public function create($data) {
        // Set default status and paid time upon creation
        $data['status'] = 'paid';
        $data['paid_at'] = date('Y-m-d H:i:s');
        
        return Database::insert('payments', $data);
    }
    
    /**
     * Update payment details (e.g., change status to refunded/failed)
     */
    public function update($id, $data) {
        return Database::update('payments', $data, 'id = ?', [$id]);
    }
}