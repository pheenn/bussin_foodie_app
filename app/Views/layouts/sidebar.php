<?php if (Auth::isLoggedIn()): ?>
<?php 
    $user = Auth::getUser(); 
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    
    // Helper function to check active state
    // Returns active class if match, inactive class if not
    function getLinkClass($uri, $path) {
        $isActive = false;
        if ($path === '/dashboard') {
            $isActive = ($uri === '/dashboard' || $uri === '/dashboard.php' || $uri === '/' || $uri === '/index.php');
        } else {
            // Check if current URI starts with the path (e.g. /products/create starts with /products)
            // Also handle .php extensions
            $uriWithoutQuery = strtok($uri, '?');
            $isActive = (strpos($uriWithoutQuery, $path) === 0 || strpos($uriWithoutQuery, $path . '.php') === 0);
        }
        
        return $isActive 
            ? 'bg-orange-500 text-white shadow-lg' 
            : 'hover:bg-gray-800 hover:text-white text-gray-300';
    }

    // Helper for icon colors
    function getIconClass($uri, $path) {
        $isActive = false;
        if ($path === '/dashboard') {
            $isActive = ($uri === '/dashboard' || $uri === '/dashboard.php' || $uri === '/' || $uri === '/index.php');
        } else {
            $uriWithoutQuery = strtok($uri, '?');
            $isActive = (strpos($uriWithoutQuery, $path) === 0 || strpos($uriWithoutQuery, $path . '.php') === 0);
        }
        
        return $isActive 
            ? 'text-white' 
            : 'text-gray-400 group-hover:text-white';
    }
?>
<div class="flex flex-col md:flex-row h-full">
    <aside id="sidebar" 
           class="sidebar-transition fixed top-0 left-0 z-30 w-64 bg-gray-900 text-white transform -translate-x-full 
                  md:sticky md:top-0 md:left-0 md:z-0 md:translate-x-0 md:flex md:flex-col md:h-screen md:min-h-screen">
        
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800 bg-gray-900 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-utensils"></i>
                </div>
                <span class="text-xl font-bold">Bussin' Foodie</span>
            </div>
            
            <button onclick="toggleSidebar()" 
                    class="md:hidden p-2 hover:bg-gray-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-700"
                    aria-label="Close sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="px-4 py-4 border-b border-gray-800 bg-gray-900 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm truncate" title="<?= e($user['name']) ?>"><?= e($user['name']) ?></h3>
                    <p class="text-xs text-gray-400 truncate"><?= e($user['role']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto overflow-x-hidden py-2">
            <nav class="space-y-1 px-2">
                <a href="<?= url('dashboard') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?= getLinkClass($currentUri, '/dashboard') ?>">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                        <i class="fas fa-tachometer-alt text-base <?= getIconClass($currentUri, '/dashboard') ?>"></i>
                    </div>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>
                
                <a href="<?= url('orders') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?= getLinkClass($currentUri, '/orders') ?>">
                    <div class="w-7 h-7 flex items-center justify-center relative shrink-0">
                        <i class="fas fa-shopping-cart text-base <?= getIconClass($currentUri, '/orders') ?>"></i>
                    </div>
                    <span class="font-medium text-sm">Orders</span>
                    <?php if (($kpis['pending_orders'] ?? 0) > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none shadow-sm">
                        <?= $kpis['pending_orders'] ?>
                    </span>
                    <?php endif; ?>
                </a>
                <a href="<?= url('payments') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                   transition-all duration-200 group <?=
                   getLinkClass($currentUri, '/payments') ?>">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                    <i class="fas fa-receipt text-base <?= getIconClass($currentUri, '/payments') ?>"></i>
                    </div>
                    <span class="font-medium text-sm">Payments</span>
                </a>
                <a href="<?= url('products') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?= getLinkClass($currentUri, '/products') ?>">
                    <div class="w-7 h-7 flex items-center justify-center relative shrink-0">
                        <i class="fas fa-hamburger text-base <?= getIconClass($currentUri, '/products') ?>"></i>
                        <?php if (($kpis['low_stock'] ?? 0) > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs w-3 h-3 flex items-center justify-center rounded-full text-[8px] border border-gray-900">!</span>
                        <?php endif; ?>
                    </div>
                    <span class="font-medium text-sm">Products</span>
                    <?php if (($kpis['low_stock'] ?? 0) > 0): ?>
                    <span class="ml-auto bg-yellow-600 text-white text-[10px] px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none">
                        <?= $kpis['low_stock'] ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= url('customers') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?= getLinkClass($currentUri, '/customers') ?>">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                        <i class="fas fa-users text-base <?= getIconClass($currentUri, '/customers') ?>"></i>
                    </div>
                    <span class="font-medium text-sm">Customers</span>
                </a>
                
                <div class="px-3 pt-4 pb-1">
                    <span class="text-xs uppercase text-gray-500 tracking-wider font-semibold">System</span>
                </div>
                
                <a href="<?= url('users') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group <?= getLinkClass($currentUri, '/users') ?>">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-cog text-base <?= getIconClass($currentUri, '/users') ?>"></i>
                    </div>
                    <span class="font-medium text-sm">Users</span>
                </a>
                
                <a href="<?= url('settings') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:bg-gray-800 hover:text-white text-gray-300">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                        <i class="fas fa-cog text-base text-gray-400 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium text-sm">Settings</span>
                </a>
                
                <a href="<?= url('logout') ?>" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 hover:bg-red-900 hover:text-white text-gray-300 group mt-4 mb-2">
                    <div class="w-7 h-7 flex items-center justify-center shrink-0">
                        <i class="fas fa-sign-out-alt text-base text-gray-400 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium text-sm">Logout</span>
                </a>
            </nav>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-800 bg-gray-900 flex-shrink-0">
            <div class="text-center text-xs text-gray-400">
                <p>© <?= date('Y') ?> Bussin' Foodie</p>
                <p class="mt-0.5">v1.0.0</p>
            </div>
        </div>
    </aside>

    <div class="flex-1 w-full md:min-h-screen overflow-y-auto bg-gray-50">
        <main id="mainContent" class="min-h-screen w-full">
<?php endif; ?>