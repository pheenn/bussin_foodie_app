<?php
/**
 * Setting Controller - Manage System Configuration
 */

class SettingController {
    private $settingModel;
    
    public function __construct() {
        Auth::check();
        require_once APP_PATH . '/Models/Setting.php';
        $this->settingModel = new Setting();
    }
    
    public function index() {
        $settings = $this->settingModel->getAll();
        
        $this->render('settings/index', [
            'settings' => $settings,
            'pageTitle' => 'System Settings'
        ]);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->redirect('/settings');
        
        try {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid CSRF token');
            }
            
            // defined allowed settings keys to prevent pollution
            $allowedKeys = ['store_name', 'store_phone', 'store_email', 'currency', 'tax_rate'];
            $data = [];
            
            foreach ($allowedKeys as $key) {
                if (isset($_POST[$key])) {
                    $data[$key] = trim($_POST[$key]);
                }
            }
            
            $this->settingModel->updateBatch($data);
            
            Flash::success('Settings updated successfully');
            $this->redirect('/settings');
            
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            $this->redirect('/settings');
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