<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Today's Orders -->
    <div class="kpi-card bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Today's Orders</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?= $kpis['today_orders'] ?></p>
                <div class="flex items-center mt-2">
                    <?php if ($kpis['today_orders'] > $kpis['yesterday_orders']): ?>
                        <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                        <span class="text-sm text-green-600">Up from yesterday</span>
                    <?php else: ?>
                        <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                        <span class="text-sm text-red-600">Down from yesterday</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Today's Revenue -->
    <div class="kpi-card bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Today's Revenue</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?= format_currency($kpis['today_revenue']) ?></p>
                <div class="flex items-center mt-2">
                    <?php if ($kpis['today_revenue'] > $kpis['yesterday_revenue']): ?>
                        <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                        <span class="text-sm text-green-600">Up from yesterday</span>
                    <?php else: ?>
                        <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                        <span class="text-sm text-red-600">Down from yesterday</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="kpi-card bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Orders</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?= $kpis['total_orders'] ?></p>
                <p class="text-sm text-gray-500 mt-2">All time</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Items -->
    <div class="kpi-card bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?= $kpis['low_stock'] ?></p>
                <p class="text-sm text-gray-500 mt-2">Needs attention</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>