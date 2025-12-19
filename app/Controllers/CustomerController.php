<?php
/**
 * Customer Controller - Manage Customer Data
 */

class CustomerController {
    private $customerModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Customer.php';
        $this->customerModel = new Customer();
    }
    
    public function index() {
        $customers = $this->customerModel->getAll();
        $this->render('customers/index', [
            'customers' => $customers, 
            'pageTitle' => 'Customer Management'
        ]);
    }
    
    public function create() {
        $this->render('customers/create', ['pageTitle' => 'Add New Customer']);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('customers/create');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            $data = $this->getValidatedData($_POST);
            
            // Validation
            $errors = $this->validateCustomer($data);
            if (!empty($errors)) {
                foreach ($errors as $error) Flash::error($error);
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('customers/create');
            }
            
            // Check uniqueness
            if ($this->customerModel->emailExists($data['email'])) {
                Flash::error('Email address is already registered.');
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('customers/create');
            }
            
            if (!empty($data['phone']) && $this->customerModel->phoneExists($data['phone'])) {
                Flash::error('Phone number is already registered.');
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('customers/create');
            }
            
            // Prepare for storage
            $customerData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->customerModel->create($customerData);
            Flash::success('Customer created successfully');
            $this->redirect('customers');
            
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('customers/create');
        }
    }
    
    public function edit($id) {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            Flash::error('Customer not found');
            return $this->redirect('customers');
        }
        
        $this->render('customers/edit', [
            'customer' => $customer, 
            'pageTitle' => 'Edit Customer: ' . $customer['name']
        ]);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('customers/edit/' . $id);
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            $customer = $this->customerModel->find($id);
            if (!$customer) throw new Exception('Customer not found');
            
            $data = $this->getValidatedData($_POST);
            
            // Validation
            $errors = $this->validateCustomer($data, true);
            if (!empty($errors)) {
                foreach ($errors as $error) Flash::error($error);
                return $this->redirect('customers/edit/' . $id);
            }
            
            // Check uniqueness (excluding current customer)
            if ($this->customerModel->emailExists($data['email'], $id)) {
                Flash::error('Email address is already registered.');
                return $this->redirect('customers/edit/' . $id);
            }
            
            if (!empty($data['phone']) && $this->customerModel->phoneExists($data['phone'], $id)) {
                Flash::error('Phone number is already registered.');
                return $this->redirect('customers/edit/' . $id);
            }
            
            $customerData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];
            
            $this->customerModel->update($id, $customerData);
            Flash::success('Customer updated successfully');
            $this->redirect('customers');
            
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('customers/edit/' . $id);
        }
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                $this->customerModel->delete($id);
                Flash::success('Customer deleted successfully.');
            } else {
                Flash::error('Invalid CSRF token');
            }
        }
        $this->redirect('customers');
    }
    
    /**
     * Sanitize and format input data
     */
    private function getValidatedData($post) {
        return [
            'name' => trim($post['name'] ?? ''),
            'email' => trim($post['email'] ?? ''),
            'phone' => trim($post['phone'] ?? ''),
            'address' => trim($post['address'] ?? ''),
        ];
    }

    /**
     * Basic client-side validation
     */
    private function validateCustomer($data, $isUpdate = false) {
        $errors = [];
        if (empty($data['name'])) $errors[] = "Customer Name is required.";
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        
        // Phone number is optional, but if provided, validate basic format
        if (!empty($data['phone']) && !preg_match('/^(\+?\d{1,4}[-\s]?)?(\d{1,4}[-\s]?){1,4}\d$/', $data['phone'])) {
            $errors[] = "Invalid phone number format.";
        }
        
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