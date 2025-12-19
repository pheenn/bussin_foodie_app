<?php
/**
 * Order Controller
 */

class OrderController {
    private $orderModel;
    private $productModel;
    private $customerModel;
    private $paymentModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Order.php';
        require_once APP_PATH . '/Models/Product.php';
        require_once APP_PATH . '/Models/Customer.php';
        require_once APP_PATH . '/Models/Payment.php';
        
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->customerModel = new Customer();
        $this->paymentModel = new Payment();
    }
    
    public function index() {
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $orders = $this->orderModel->getAll(['status' => $status, 'search' => $search]);
        $stats = $this->orderModel->getStats();
        
        $this->render('orders/index', [
            'orders' => $orders,
            'stats' => $stats,
            'currentStatus' => $status,
            'search' => $search,
            'pageTitle' => 'Orders'
        ]);
    }

    public function view($id) {
        $order = $this->orderModel->find($id);
        if (!$order) {
            Flash::error("Order not found");
            return $this->redirect('/orders');
        }

        $items = $this->orderModel->getItems($id);
        $payments = $this->paymentModel->getByOrderId($id);
        
        $totalPaid = 0;
        foreach ($payments as $p) {
            if ($p['status'] == 'paid') $totalPaid += $p['amount'];
        }
        $balance = $order['total_amount'] - $totalPaid;

        $this->render('orders/view', [
            'order' => $order, 
            'items' => $items, 
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
            'pageTitle' => 'Order ' . $order['order_number']
        ]);
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->orderModel->updateStatus($_POST['order_id'], $_POST['status']);
            Flash::success("Status updated");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        $this->redirect('/orders');
    }

    public function create() {
        $customers = $this->customerModel->getAll();
        $products = $this->productModel->getAll(['active' => true]);
        
        $this->render('orders/create', [
            'customers' => $customers,
            'products' => $products,
            'pageTitle' => 'Create New Order'
        ]);
    }

    /**
     * Store Order with Stock Management & Seat Number
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/orders/create');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) throw new Exception('Invalid CSRF token');
            
            $customer = $this->customerModel->find($_POST['customer_id']);
            if (!$customer) throw new Exception("Invalid Customer");

            // Process items and Validate Stock
            $itemsData = $this->processItems($_POST['items'] ?? []);
            if (empty($itemsData['items'])) throw new Exception("Order must have at least one item.");

            // Check if sufficient stock exists for all items
            foreach ($itemsData['items'] as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$product['name']}. Available: {$product['stock_quantity']}");
                }
            }

            // Prepare Order Data
            $orderData = [
                'customer_id' => $customer['id'],
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_phone' => $customer['phone'],
                'seat_number' => trim($_POST['seat_number'] ?? ''), // New Field
                'total_amount' => $itemsData['total'],
                'status' => 'pending',
                'notes' => $_POST['notes'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // 1. Create Order
            $orderId = $this->orderModel->create($orderData);

            // 2. Add Items & Deduct Stock
            $this->orderModel->addItems($orderId, $itemsData['items']);
            
            foreach ($itemsData['items'] as $item) {
                $product = $this->productModel->find($item['product_id']);
                $newStock = $product['stock_quantity'] - $item['quantity'];
                $this->productModel->updateStock($product['id'], $newStock);
            }

            Flash::success("Order created successfully!");
            $this->redirect('/orders/view/' . $orderId);

        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/orders/create');
        }
    }

    public function edit($id) {
        $order = $this->orderModel->find($id);
        if (!$order) {
            Flash::error("Order not found");
            return $this->redirect('/orders');
        }

        $orderItems = $this->orderModel->getItems($id);
        $customers = $this->customerModel->getAll();
        $products = $this->productModel->getAll(['active' => true]);

        $this->render('orders/edit', [
            'order' => $order,
            'orderItems' => $orderItems,
            'customers' => $customers,
            'products' => $products,
            'pageTitle' => 'Edit Order'
        ]);
    }

    /**
     * Update Order with Stock Restoration
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/orders/edit/' . $id);

        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) throw new Exception('Invalid CSRF token');

            $customer = $this->customerModel->find($_POST['customer_id']);
            $itemsData = $this->processItems($_POST['items'] ?? []);
            
            if (empty($itemsData['items'])) throw new Exception("Order must have at least one item.");

            // 1. Restore Stock from OLD items
            $oldItems = $this->orderModel->getItems($id);
            foreach ($oldItems as $oldItem) {
                $product = $this->productModel->find($oldItem['product_id']);
                // If product still exists, add stock back
                if ($product) {
                    $restoredStock = $product['stock_quantity'] + $oldItem['quantity'];
                    $this->productModel->updateStock($product['id'], $restoredStock);
                }
            }

            // 2. Validate Stock for NEW items
            foreach ($itemsData['items'] as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product['stock_quantity'] < $item['quantity']) {
                    // Re-deduct old items if validation fails (Rollback simulation)
                    foreach ($oldItems as $oldItem) {
                        $p = $this->productModel->find($oldItem['product_id']);
                        if($p) $this->productModel->updateStock($p['id'], $p['stock_quantity'] - $oldItem['quantity']);
                    }
                    throw new Exception("Insufficient stock for {$product['name']}. Available: {$product['stock_quantity']}");
                }
            }

            $orderData = [
                'customer_id' => $customer['id'],
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_phone' => $customer['phone'],
                'seat_number' => trim($_POST['seat_number'] ?? ''), // New Field
                'total_amount' => $itemsData['total'],
                'notes' => $_POST['notes'] ?? '',
                'status' => $_POST['status']
            ];

            // 3. Update Order
            $this->orderModel->update($id, $orderData);

            // 4. Replace Items & Deduct New Stock
            $this->orderModel->clearItems($id);
            $this->orderModel->addItems($id, $itemsData['items']);
            
            foreach ($itemsData['items'] as $item) {
                $product = $this->productModel->find($item['product_id']);
                $newStock = $product['stock_quantity'] - $item['quantity'];
                $this->productModel->updateStock($product['id'], $newStock);
            }

            Flash::success("Order updated successfully!");
            $this->redirect('/orders/view/' . $id);

        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/orders/edit/' . $id);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            // Restore stock before deleting
            $oldItems = $this->orderModel->getItems($id);
            foreach ($oldItems as $oldItem) {
                $product = $this->productModel->find($oldItem['product_id']);
                if ($product) {
                    $this->productModel->updateStock($product['id'], $product['stock_quantity'] + $oldItem['quantity']);
                }
            }
            
            $this->orderModel->delete($id);
            Flash::success("Order deleted and stock restored.");
        }
        $this->redirect('/orders');
    }

    private function processItems($rawItems) {
        $processed = [];
        $total = 0;

        foreach ($rawItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $qty = (int)$item['quantity'];
                if ($qty > 0) {
                    $price = (float)$product['price'];
                    $subtotal = $price * $qty;
                    
                    $processed[] = [
                        'product_id' => $product['id'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal
                    ];
                    $total += $subtotal;
                }
            }
        }
        return ['items' => $processed, 'total' => $total];
    }

    private function render($view, $data = []) {
        extract($data);
        require_once VIEWS_PATH . '/layouts/header.php';
        require_once VIEWS_PATH . '/layouts/sidebar.php';
        require_once VIEWS_PATH . '/' . $view . '.php';
        require_once VIEWS_PATH . '/layouts/footer.php';
    }
    
    private function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}