<div class="min-h-full-screen">
    <!-- Page header with mobile menu button -->
    <header class="bg-white shadow md:shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3 md:py-4">
            <div class="flex items-center">
                <!-- Mobile: Centered title with space for hamburger -->
                <div class="md:hidden w-full">
                    <div class="flex items-center justify-center ml-10">
                        <div class="text-center">
                            <h1 class="text-lg font-bold text-gray-900">Dashboard</h1>
                            <p class="text-xs text-gray-600 mt-0.5"><?= date('M j, Y') ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Desktop page title -->
                <div class="hidden md:block flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                            <p class="mt-1 text-sm text-gray-600">Welcome back, <?= e(Auth::getUser()['name']) ?>! Here's what's happening today.</p>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Notifications (placeholder) -->
                            <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="fas fa-bell"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <!-- User profile dropdown (placeholder) -->
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div class="text-right hidden lg:block">
                                    <p class="text-sm font-medium text-gray-900"><?= e(Auth::getUser()['name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= e(Auth::getUser()['role']) ?></p>
                                </div>
                            </div>
                            
                            <!-- Date and status badge -->
                            <div class="hidden lg:flex items-center space-x-3">
                                <span class="text-sm text-gray-500"><?= date('l, F j, Y') ?></span>
                                <div class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-circle mr-1 text-xs"></i> Live
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main content -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <!-- KPI Cards -->
        <?php include VIEWS_PATH . '/partials/kpi_cards.php'; ?>
        
        <!-- Main Grid -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Recent Orders (takes 2 columns on desktop) -->
            <div class="lg:col-span-2">
                <!-- Recent Orders -->
                <?php include VIEWS_PATH . '/partials/recent_orders.php'; ?>
                
            </div>
            
            <!-- Right Column: Stats & Alerts -->
            <div class="space-y-8">
                <!-- Low Stock Alert -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        Low Stock Alert
                    </h2>
                    <div class="space-y-4">
                        <?php if (!empty($lowStockProducts)): ?>
                            <?php foreach ($lowStockProducts as $product): ?>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" 
                                             class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="font-medium text-gray-900 truncate" title="<?= e($product['name']) ?>">
                                                <?= e($product['name']) ?>
                                            </h4>
                                            <p class="text-sm text-gray-500 truncate"><?= e($product['category_name']) ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-lg font-bold text-red-600"><?= $product['stock_quantity'] ?></span>
                                        <p class="text-xs text-gray-500">in stock</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <a href="/products" class="block text-center text-orange-600 font-medium hover:text-orange-700 mt-4 transition-colors">
                                View All Products →
                            </a>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                <p class="text-gray-600">All products are sufficiently stocked</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Top Selling Products -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h2>
                    <div class="space-y-4">
                        <?php foreach ($topProducts as $index => $product): ?>
                            <div class="flex items-center justify-between hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full text-sm font-medium flex-shrink-0">
                                        <?= $index + 1 ?>
                                    </span>
                                    <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" 
                                         class="w-10 h-10 object-cover rounded-lg flex-shrink-0">
                                    <div class="min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate" title="<?= e($product['name']) ?>">
                                            <?= e($product['name']) ?>
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate"><?= e($product['category']) ?></p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="font-medium text-sm"><?= $product['total_sold'] ?> sold</span>
                                    <p class="text-xs text-gray-500"><?= format_currency($product['total_revenue']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>