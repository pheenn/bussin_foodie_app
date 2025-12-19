<?php
/**
 * Product Model - Full CRUD functionality
 */

class Product {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    /**
     * Get all products with optional filters
     */
    public function getAll($filters = []) {
        $where = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['in_stock'])) {
            $where .= " AND p.stock_quantity > 0";
        }
        
        if (!empty($filters['low_stock'])) {
            $where .= " AND p.stock_quantity <= p.min_stock AND p.stock_quantity > 0";
        }
        
        if (!empty($filters['out_of_stock'])) {
            $where .= " AND p.stock_quantity = 0";
        }
        
        if (!empty($filters['active'])) {
            $where .= " AND p.is_active = 1";
        }
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   CASE 
                       WHEN p.stock_quantity = 0 THEN 'out_of_stock'
                       WHEN p.stock_quantity <= p.min_stock THEN 'low_stock'
                       ELSE 'in_stock'
                   END as stock_status
            FROM products p
            JOIN categories c ON p.category_id = c.id
            $where
            ORDER BY p.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Find product by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new product
     */
    public function create($data) {
        $defaults = [
            'is_active' => 1,
            'cost_price' => 0,
            'image_url' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $data = array_merge($defaults, $data);
        
        return Database::insert('products', $data);
    }
    
    /**
     * Update product
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('products', $data, 'id = ?', [$id]);
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        return Database::delete('products', 'id = ?', [$id]);
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY display_order, name");
        return $stmt->fetchAll();
    }
    
    /**
     * Update stock quantity
     */
    public function updateStock($id, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
    
    /**
     * Get product statistics
     */
    public function getStats() {
        $stats = [
            'total_products' => 0,
            'active_products' => 0,
            'out_of_stock' => 0,
            'low_stock' => 0,
            'total_value' => 0
        ];
        
        try {
            // Total products
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products");
            $result = $stmt->fetch();
            $stats['total_products'] = $result['count'];
            
            // Active products
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
            $result = $stmt->fetch();
            $stats['active_products'] = $result['count'];
            
            // Out of stock
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0 AND is_active = 1");
            $result = $stmt->fetch();
            $stats['out_of_stock'] = $result['count'];
            
            // Low stock
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity <= min_stock AND stock_quantity > 0 AND is_active = 1");
            $result = $stmt->fetch();
            $stats['low_stock'] = $result['count'];
            
            // Total inventory value
            $stmt = $this->db->query("SELECT SUM(stock_quantity * cost_price) as total FROM products WHERE is_active = 1");
            $result = $stmt->fetch();
            $stats['total_value'] = $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Product stats error: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Search products
     */
    public function search($query) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY p.name
            LIMIT 20
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}