<?php


class PaymentController {
    private $paymentModel;
    private $orderModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Payment.php';
        require_once APP_PATH . '/Models/Order.php';
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
    }
    
    /**
     * Display all payments
     */
    public function index() {
        $payments = $this->paymentModel->getAll();
        
        $this->render('payments/index', [
            'payments' => $payments,
            'pageTitle' => 'Payment History'
        ]);
    }
    
    /**
     * Record a new payment for a specific order.
     * Route: POST /payments/record/{orderId}
     */
    public function recordPayment($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/orders/view/' . $orderId);
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                Flash::error('Order not found.');
                return $this->redirect('/orders');
            }
            
            $amount = (float)($_POST['amount'] ?? 0);
            $method = $_POST['payment_method'] ?? 'cash';
            $transactionId = trim($_POST['transaction_id'] ?? null);
            
            if ($amount <= 0) {
                throw new Exception('Payment amount must be greater than zero.');
            }
            
            // 1. Record the payment
            $paymentData = [
                'order_id' => $orderId,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_id' => $transactionId ?: null
            ];
            $this->paymentModel->create($paymentData);
            
            // 2. Update Order status
            // Calculate total paid amount for the order
            $payments = $this->paymentModel->getByOrderId($orderId);
            $totalPaid = array_sum(array_column($payments, 'amount'));
            
            $newStatus = ($totalPaid >= $order['total_amount']) ? 'completed' : 'partially_paid';
            
            // Update order status (assuming Order model has an update method)
            $this->orderModel->update($orderId, ['status' => $newStatus]);
            
            Flash::success("Payment of " . format_currency($amount) . " recorded successfully. Order status updated to '{$newStatus}'.");
            $this->redirect('/orders/view/' . $orderId); // Assumes an order view route exists
            
        } catch (Exception $e) {
            error_log("Payment record error: " . $e->getMessage());
            Flash::error('Error recording payment: ' . $e->getMessage());
            $this->redirect('/orders/view/' . $orderId);
        }
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