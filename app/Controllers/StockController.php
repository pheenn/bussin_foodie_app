<?php
/**
 * Stock Controller for inventory management
 */

class StockController {
    private $productModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Product.php';
        $this->productModel = new Product();
    }
    
    /**
     * Update product stock
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('products');
            return;
        }
        
        try {
            // Validate CSRF token
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                Flash::error('Invalid CSRF token');
                $this->redirect('products');
                return;
            }
            
            // Get current product
            $product = $this->productModel->find($id);
            if (!$product) {
                Flash::error('Product not found');
                $this->redirect('products');
                return;
            }
            
            // Get stock data
            $currentStock = $product['stock_quantity'];
            $newStock = (int)$_POST['stock_quantity'];
            $action = $_POST['stock_action'] ?? 'set';
            
            // Calculate new stock based on action
            switch ($action) {
                case 'add':
                    $finalStock = $currentStock + $newStock;
                    break;
                case 'subtract':
                    $finalStock = $currentStock - $newStock;
                    if ($finalStock < 0) $finalStock = 0;
                    break;
                case 'set':
                default:
                    $finalStock = $newStock;
                    break;
            }
            
            // Update stock
            $result = $this->productModel->updateStock($id, $finalStock);
            
            if ($result) {
                Flash::success('Stock updated successfully!');
            } else {
                Flash::error('Failed to update stock');
            }
            
        } catch (Exception $e) {
            error_log("Stock update error: " . $e->getMessage());
            Flash::error('Error updating stock: ' . $e->getMessage());
        }
        
        $this->redirect('products');
    }
    
    private function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }
}