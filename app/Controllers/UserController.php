<?php
/**
 * User Controller - Manage System Users
 */

class UserController {
    private $userModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/User.php';
        $this->userModel = new User();
    }
    
    public function index() {
        $users = $this->userModel->getAll();
        $this->render('users/index', [
            'users' => $users, 
            'pageTitle' => 'User Management'
        ]);
    }
    
    public function create() {
        $this->render('users/create', ['pageTitle' => 'Add New User']);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/users/create');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            $data = [
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'role' => $_POST['role']
            ];
            
            // Validation
            $errors = $this->validateUser($data);
            if (!empty($errors)) {
                foreach ($errors as $error) Flash::error($error);
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('/users/create');
            }
            
            // Check uniqueness
            if ($this->userModel->usernameExists($data['username'])) {
                Flash::error('Username already taken');
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('/users/create');
            }
            
            if ($this->userModel->emailExists($data['email'])) {
                Flash::error('Email already registered');
                $_SESSION['old_input'] = $_POST;
                return $this->redirect('/users/create');
            }
            
            // Prepare for storage
            $userData = [
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->userModel->create($userData);
            Flash::success('User created successfully');
            $this->redirect('/users');
            
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/users/create');
        }
    }
    
    public function edit($id) {
        $user = $this->userModel->find($id);
        if (!$user) {
            Flash::error('User not found');
            return $this->redirect('/users');
        }
        
        $this->render('users/edit', [
            'user' => $user, 
            'pageTitle' => 'Edit User: ' . $user['username']
        ]);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/users/edit/' . $id);
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            $user = $this->userModel->find($id);
            if (!$user) throw new Exception('User not found');
            
            $data = [
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'role' => $_POST['role']
            ];
            
            // Validate Basic Info
            if (empty($data['full_name'])) Flash::error('Full Name is required');
            if (empty($data['username'])) Flash::error('Username is required');
            if (empty($data['email'])) Flash::error('Email is required');
            
            // Check duplicates (excluding current user)
            if ($this->userModel->usernameExists($data['username'], $id)) {
                Flash::error('Username already taken');
                return $this->redirect('/users/edit/' . $id);
            }
            if ($this->userModel->emailExists($data['email'], $id)) {
                Flash::error('Email already taken');
                return $this->redirect('/users/edit/' . $id);
            }
            
            // Handle Password Update
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    Flash::error('Password must be at least 6 characters');
                    return $this->redirect('/users/edit/' . $id);
                }
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    Flash::error('Passwords do not match');
                    return $this->redirect('/users/edit/' . $id);
                }
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $this->userModel->update($id, $data);
            Flash::success('User updated successfully');
            $this->redirect('/users');
            
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/users/edit/' . $id);
        }
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                // Prevent self-deletion
                if ($id == $_SESSION['admin_id']) {
                    Flash::error("You cannot delete your own account.");
                } else {
                    $this->userModel->delete($id);
                    Flash::success('User deleted successfully.');
                }
            }
        }
        $this->redirect('/users');
    }
    
    private function validateUser($data) {
        $errors = [];
        if (empty($data['full_name'])) $errors[] = "Full Name is required";
        if (strlen($data['username']) < 4) $errors[] = "Username must be at least 4 chars";
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        if (strlen($data['password']) < 6) $errors[] = "Password must be at least 6 chars";
        if ($data['password'] !== $data['confirm_password']) $errors[] = "Passwords do not match";
        return $errors;
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