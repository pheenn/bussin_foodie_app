<?php
/**
 * Order Model
 */

class Order {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    // ... (Keep existing getAll, find, getItems, updateStatus, getStats methods) ...
    public function getAll($filters = []) {
        $sql = "
            SELECT o.*, c.name as linked_customer_name, 
                   COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE 1=1
        ";
        
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE ? OR o.customer_name LIKE ?)";
            $term = "%" . $filters['search'] . "%";
            $params[] = $term;
            $params[] = $term;
        }
        
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT o.*, c.name as linked_customer_name, c.email as linked_customer_email, 
                   c.phone as linked_customer_phone, c.address as linked_customer_address
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItems($orderId) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.image_url, p.category_id, p.price as current_price
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        return Database::update('orders', ['status' => $status], 'id = ?', [$id]);
    }

    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM orders
        ");
        return $stmt->fetch();
    }

    // --- NEW METHODS FOR CRUD ---

    /**
     * Create the order header
     */
    public function create($data) {
        // Generate Order Number if not provided
        if (empty($data['order_number'])) {
            $data['order_number'] = 'ORD-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
        }
        return Database::insert('orders', $data);
    }

    /**
     * Add items to an order
     */
    public function addItems($orderId, $items) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($items as $item) {
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['subtotal']
            ]);
        }
    }

    /**
     * Update order details
     */
    public function update($id, $data) {
        return Database::update('orders', $data, 'id = ?', [$id]);
    }

    /**
     * Clear items (used before re-adding them during update)
     */
    public function clearItems($orderId) {
        return Database::delete('order_items', 'order_id = ?', [$orderId]);
    }

    /**
     * Delete entire order
     */
    public function delete($id) {
        return Database::delete('orders', 'id = ?', [$id]);
    }
}