<?php
/**
 * Dashboard Model for statistics and analytics
 */

class Dashboard {
    private $db;
    
    public function __construct() {
        require_once APP_PATH . '/Helpers/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getKPIs() {
        $kpis = [
            'today_orders' => 0,
            'today_revenue' => 0,
            'yesterday_orders' => 0,
            'yesterday_revenue' => 0,
            'total_orders' => 0,
            'total_revenue' => 0,
            'active_products' => 0,
            'low_stock' => 0,
            'pending_orders' => 0
        ];
        
        try {
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            
            // Today's orders and revenue
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue 
                FROM orders 
                WHERE DATE(created_at) = ?
            ");
            $stmt->execute([$today]);
            $todayData = $stmt->fetch();
            $kpis['today_orders'] = $todayData['orders'];
            $kpis['today_revenue'] = $todayData['revenue'];
            
            // Yesterday's orders and revenue
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue 
                FROM orders 
                WHERE DATE(created_at) = ?
            ");
            $stmt->execute([$yesterday]);
            $yesterdayData = $stmt->fetch();
            $kpis['yesterday_orders'] = $yesterdayData['orders'];
            $kpis['yesterday_revenue'] = $yesterdayData['revenue'];
            
            // Total orders and revenue
            $stmt = $this->db->query("
                SELECT COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue 
                FROM orders 
                WHERE status != 'cancelled'
            ");
            $totalData = $stmt->fetch();
            $kpis['total_orders'] = $totalData['orders'];
            $kpis['total_revenue'] = $totalData['revenue'];
            
            // Active products
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
            $activeProducts = $stmt->fetch();
            $kpis['active_products'] = $activeProducts['count'];
            
            // Low stock products
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM products 
                WHERE stock_quantity <= min_stock
            ");
            $lowStock = $stmt->fetch();
            $kpis['low_stock'] = $lowStock['count'];
            
            // Pending orders
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM orders 
                WHERE status IN ('pending', 'confirmed', 'preparing')
            ");
            $pending = $stmt->fetch();
            $kpis['pending_orders'] = $pending['count'];
            
        } catch (Exception $e) {
            error_log("getKPIs error: " . $e->getMessage());
        }
        
        return $kpis;
    }
    
    public function getRecentOrders($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, 
                       COUNT(oi.id) as item_count,
                       GROUP_CONCAT(p.name SEPARATOR ', ') as item_names
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("getRecentOrders error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getLowStockProducts() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.stock_quantity <= p.min_stock 
                    AND p.is_active = 1
                ORDER BY p.stock_quantity ASC
                LIMIT 10
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("getLowStockProducts error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTopSellingProducts($limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.name,
                    p.image_url,
                    c.name as category,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.subtotal) as total_revenue
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN categories c ON p.category_id = c.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status != 'cancelled'
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("getTopSellingProducts error: " . $e->getMessage());
            return [];
        }
    }
}