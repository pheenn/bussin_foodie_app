<?php
/**
 * Product Controller - Full CRUD functionality
 */

class ProductController {
    private $productModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Product.php';
        $this->productModel = new Product();
    }
    
    /**
     * Display all products
     */
    public function index() {
        try {
            $products = $this->productModel->getAll();
            $categories = $this->productModel->getCategories();
            
            // Filters
            $search = $_GET['search'] ?? '';
            $category_id = $_GET['category_id'] ?? '';
            $stock_filter = $_GET['stock_filter'] ?? '';
            
            $filters = [];
            if (!empty($search)) $filters['search'] = $search;
            if (!empty($category_id) && $category_id !== 'all') $filters['category_id'] = $category_id;
            if (!empty($stock_filter)) {
                switch ($stock_filter) {
                    case 'in_stock': $filters['in_stock'] = true; break;
                    case 'low_stock': $filters['low_stock'] = true; break;
                    case 'out_of_stock': $filters['out_of_stock'] = true; break;
                }
            }
            
            $products = $this->productModel->getAll($filters);
            $stats = $this->productModel->getStats();
            
            $viewData = [
                'products' => $products,
                'categories' => $categories,
                'stats' => $stats,
                'search' => $search,
                'selected_category' => $category_id,
                'selected_stock_filter' => $stock_filter,
                'pageTitle' => 'Product Management'
            ];
            
            $this->render('products/index', $viewData);
            
        } catch (Exception $e) {
            error_log("Product index error: " . $e->getMessage());
            Flash::error('Error loading products: ' . $e->getMessage());
            $this->redirect('products');
        }
    }
    
    /**
     * Show create product form
     */
    public function create() {
        $categories = $this->productModel->getCategories();
        $this->render('products/create', [
            'categories' => $categories,
            'pageTitle' => 'Add New Product'
        ]);
    }
    
    /**
     * Store new product with Image Upload
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('products/create');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                Flash::error('Invalid CSRF token');
                return $this->redirect('products/create');
            }
            
            // Validate basic inputs
            $errors = $this->validateProduct($_POST);
            if (!empty($errors)) {
                foreach ($errors as $error) Flash::error($error);
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('products/create');
            }
            
            // Handle Image Upload
            $imagePath = '';
            // If user provided a URL string (fallback)
            if (!empty($_POST['image_url'])) {
                $imagePath = trim($_POST['image_url']);
            }
            // If user uploaded a file (Priority)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $imagePath = $this->handleImageUpload($_FILES['image']);
                } catch (Exception $e) {
                    Flash::error($e->getMessage());
                    $_SESSION['old_input'] = $_POST;
                    return $this->redirect('products/create');
                }
            }
            
            // Prepare data
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'cost_price' => (float)($_POST['cost_price'] ?? 0),
                'stock_quantity' => (int)$_POST['stock_quantity'],
                'min_stock' => (int)($_POST['min_stock'] ?? 10),
                'category_id' => (int)$_POST['category_id'],
                'image_url' => $imagePath,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            $this->productModel->create($data);
            Flash::success('Product created successfully!');
            $this->redirect('products');
            
        } catch (Exception $e) {
            error_log("Product store error: " . $e->getMessage());
            Flash::error('Error creating product: ' . $e->getMessage());
            $_SESSION['old_input'] = $_POST;
            $this->redirect('products/create');
        }
    }
    
    /**
     * Show edit product form
     */
    public function edit($id) {
        $product = $this->productModel->find($id);
        if (!$product) {
            Flash::error('Product not found');
            return $this->redirect('products');
        }
        $categories = $this->productModel->getCategories();
        $this->render('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'pageTitle' => 'Edit Product: ' . $product['name']
        ]);
    }
    
    /**
     * Update existing product with Image Upload & Cleanup
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('products/edit/' . $id);
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                Flash::error('Invalid CSRF token');
                return $this->redirect('products/edit/' . $id);
            }
            
            $product = $this->productModel->find($id);
            if (!$product) {
                Flash::error('Product not found');
                return $this->redirect('products');
            }
            
            // Validate input
            $errors = $this->validateProduct($_POST, $id);
            if (!empty($errors)) {
                foreach ($errors as $error) Flash::error($error);
                return $this->redirect('products/edit/' . $id);
            }
            
            // Handle Image Upload
            $imagePath = $product['image_url']; // Default to existing
            
            // If user provided a URL string manually
            if (!empty($_POST['image_url']) && $_POST['image_url'] !== $imagePath) {
                $imagePath = trim($_POST['image_url']);
            }
            
            // If user uploaded a new file (Overrides everything)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    // Upload new image
                    $newImagePath = $this->handleImageUpload($_FILES['image']);
                    
                    // Delete old image if it was a local file
                    $this->deleteImageFile($product['image_url']);
                    
                    $imagePath = $newImagePath;
                } catch (Exception $e) {
                    Flash::error($e->getMessage());
                    return $this->redirect('products/edit/' . $id);
                }
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'cost_price' => (float)($_POST['cost_price'] ?? 0),
                'stock_quantity' => (int)$_POST['stock_quantity'],
                'min_stock' => (int)($_POST['min_stock'] ?? 10),
                'category_id' => (int)$_POST['category_id'],
                'image_url' => $imagePath,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            $this->productModel->update($id, $data);
            Flash::success('Product updated successfully!');
            $this->redirect('products');
            
        } catch (Exception $e) {
            error_log("Product update error: " . $e->getMessage());
            Flash::error('Error updating product: ' . $e->getMessage());
            $this->redirect('products/edit/' . $id);
        }
    }
    
    /**
     * Delete product AND its image
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('products');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                Flash::error('Invalid CSRF token');
                return $this->redirect('products');
            }
            
            // 1. Find product to get image path
            $product = $this->productModel->find($id);
            
            if ($product) {
                // 2. Delete the image file if it exists locally
                $this->deleteImageFile($product['image_url']);
                
                // 3. Delete database record
                $this->productModel->delete($id);
                Flash::success('Product deleted successfully!');
            } else {
                Flash::error('Product not found');
            }
            
            $this->redirect('products');
            
        } catch (Exception $e) {
            Flash::error('Error deleting product: ' . $e->getMessage());
            $this->redirect('products');
        }
    }
    
    /**
     * Helper to delete physical image file
     */
    private function deleteImageFile($path) {
        if (empty($path)) return;
        
        // Only attempt to delete if it looks like a local storage path
        if (strpos($path, '/storage/products/') === 0) {
            // Convert web path to file system path
            // PUBLIC_PATH is defined in index.php
            $filePath = PUBLIC_PATH . $path;
            
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    /**
     * Handle Image Upload Logic
     */
    private function handleImageUpload($file) {
        // Define upload path: public/storage/products/
        $uploadDir = dirname(dirname(__DIR__)) . '/public/storage/products/';
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            throw new Exception('File is too large. Max 5MB.');
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_') . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Return web-accessible path
            return '/storage/products/' . $filename;
        }
        
        throw new Exception('Failed to upload image.');
    }
    
    /**
     * Validate product data
     */
    private function validateProduct($data, $id = null) {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Product name is required';
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) $errors[] = 'Price must be a positive number';
        if (empty($data['category_id'])) $errors[] = 'Category is required';
        return $errors;
    }
    
    private function render($view, $data = []) {
        extract($data);
        require_once VIEWS_PATH . '/layouts/header.php';
        require_once VIEWS_PATH . '/layouts/sidebar.php';
        require_once VIEWS_PATH . '/' . $view . '.php';
        require_once VIEWS_PATH . '/layouts/footer.php';
    }
    
    private function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }
}