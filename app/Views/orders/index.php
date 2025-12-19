<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4 flex justify-between items-center">
            <h1 class="pl-16 text-2xl font-bold text-gray-900">Orders</h1>
            <a href="<?= url('orders/create') ?>" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 shadow-sm transition-all">
                <i class="fas fa-plus mr-2"></i> New Order
            </a>
        </div>
    </header>
    
    <div class="px-4 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase">Total Orders</p>
                <p class="text-2xl font-bold"><?= $stats['total'] ?></p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase">Pending</p>
                <p class="text-2xl font-bold"><?= $stats['pending'] ?></p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-indigo-500">
                <p class="text-xs text-gray-500 uppercase">Preparing</p>
                <p class="text-2xl font-bold"><?= $stats['preparing'] ?></p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase">Completed</p>
                <p class="text-2xl font-bold"><?= $stats['completed'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" action="<?= url('orders') ?>" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search Order # or Customer..." 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500">
                </div>
                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $currentStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $currentStatus == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="preparing" <?= $currentStatus == 'preparing' ? 'selected' : '' ?>>Preparing</option>
                        <option value="ready" <?= $currentStatus == 'ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="completed" <?= $currentStatus == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $currentStatus == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Filter</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-orange-600">
                                <a href="<?= url('orders/view/' . $order['id']) ?>"><?= e($order['order_number']) ?></a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= e($order['linked_customer_name'] ?? $order['customer_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= $order['item_count'] ?> items</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= get_status_badge($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= format_currency($order['total_amount']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('M j, H:i', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="<?= url('orders/edit/' . $order['id']) ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= url('orders/view/' . $order['id']) ?>" class="text-gray-400 hover:text-gray-600" title="View">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>