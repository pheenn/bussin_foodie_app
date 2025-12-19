<?php
/**
 * Dashboard Controller
 */

class DashboardController {
    private $dashboardModel;
    
    public function __construct() {
        // Check authentication for all dashboard routes
        Auth::check();
        
        // Load model
        require_once APP_PATH . '/Models/Dashboard.php';
        $this->dashboardModel = new Dashboard();
    }
    
    public function index() {
        try {
            // Get KPI data
            $kpis = $this->dashboardModel->getKPIs();
            
            // Get recent orders
            $recentOrders = $this->dashboardModel->getRecentOrders(10);
            
            // Get low stock products
            $lowStockProducts = $this->dashboardModel->getLowStockProducts();
            
            // Get top selling products
            $topProducts = $this->dashboardModel->getTopSellingProducts(5);
        } catch (Exception $e) {
            // If there's a database error, use empty data and log it
            error_log("Dashboard data error: " . $e->getMessage());
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
            $recentOrders = [];
            $lowStockProducts = [];
            $topProducts = [];
            
            Flash::error('Unable to load dashboard data. Please check database connection.');
        }
        
        // Load dashboard view
        $viewData = [
            'kpis' => $kpis,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
            'pageTitle' => 'Dashboard Overview'
        ];
        
        $this->render('dashboard/index', $viewData);
    }
    
    private function render($view, $data = []) {
        extract($data);
        
        // Include header
        require_once VIEWS_PATH . '/layouts/header.php';
        
        // Include sidebar
        require_once VIEWS_PATH . '/layouts/sidebar.php';
        
        // Include main content
        require_once VIEWS_PATH . '/' . $view . '.php';
        
        // Include footer
        require_once VIEWS_PATH . '/layouts/footer.php';
    }
}