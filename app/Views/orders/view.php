<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="<?= url('orders') ?>" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= e($order['order_number']) ?></h1>
                    <p class="text-sm text-gray-500"><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <form action="<?= url('orders/update-status') ?>" method="POST" class="inline-flex">
                    <?= CSRF::getTokenField() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    
                    <?php if ($order['status'] === 'pending'): ?>
                        <button name="status" value="confirmed" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Confirm Order</button>
                    <?php elseif ($order['status'] === 'confirmed'): ?>
                        <button name="status" value="preparing" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Start Preparing</button>
                    <?php elseif ($order['status'] === 'preparing'): ?>
                        <button name="status" value="ready" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Mark Ready</button>
                    <?php elseif ($order['status'] === 'ready'): ?>
                        <button name="status" value="completed" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Complete</button>
                    <?php endif; ?>
                    
                    <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                        <button name="status" value="cancelled" class="ml-2 px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50" onclick="return confirm('Cancel this order?')">Cancel</button>
                    <?php endif; ?>
                </form>
                <a href="<?= url('orders/edit/<?= $order['id'] ?>') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>
    </header>

    <div class="px-4 py-6 max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b flex justify-between items-center">
                        <h2 class="font-semibold text-gray-900">Order Items</h2>
                        <span class="text-sm font-medium <?= get_status_badge($order['status']) ?> px-3 py-1 rounded-full">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                    <div class="divide-y">
                        <?php foreach ($items as $item): ?>
                        <div class="p-4 flex items-center gap-4">
                            <img src="<?= e($item['image_url'] ?? '/assets/default.png') ?>" class="w-16 h-16 object-cover rounded-lg bg-gray-100">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900"><?= e($item['product_name']) ?></h3>
                                <p class="text-sm text-gray-500"><?= format_currency($item['unit_price']) ?> x <?= $item['quantity'] ?></p>
                            </div>
                            <div class="text-right font-medium text-gray-900">
                                <?= format_currency($item['subtotal']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="bg-gray-50 p-6 border-t">
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>Total Amount</span>
                            <span><?= format_currency($order['total_amount']) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if ($order['notes']): ?>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Order Notes</h3>
                    <p class="text-gray-600 bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <?= nl2br(e($order['notes'])) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Payment Details</h3>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Due:</span>
                            <span class="font-medium"><?= format_currency($order['total_amount']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Amount Paid:</span>
                            <span class="font-medium text-green-600"><?= format_currency($totalPaid) ?></span>
                        </div>
                        <div class="flex justify-between text-base font-bold pt-2 border-t">
                            <span class="text-gray-800">Balance:</span>
                            <span class="<?= $balance > 0 ? 'text-red-600' : 'text-gray-800' ?>">
                                <?= format_currency($balance) ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($balance > 0 && $order['status'] != 'cancelled'): ?>
                    <form action="<?= url('payments/record/<?= $order['id'] ?>') ?>" method="POST" class="mb-6 bg-gray-50 p-4 rounded-lg border">
                        <?= CSRF::getTokenField() ?>
                        <h4 class="text-sm font-bold text-gray-700 mb-3">Record New Payment</h4>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                    <input type="number" name="amount" step="0.01" max="<?= $balance ?>" value="<?= $balance ?>" required 
                                           class="w-full pl-8 pr-3 py-2 border rounded text-sm focus:ring-orange-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Payment Method</label>
                                <select name="payment_method" class="w-full px-3 py-2 border rounded text-sm focus:ring-orange-500">
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">Maya</option>
                                    <option value="card">Credit/Debit Card</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Transaction ID (Optional)</label>
                                <input type="text" name="transaction_id" placeholder="Ref No." class="w-full px-3 py-2 border rounded text-sm focus:ring-orange-500">
                            </div>
                            
                            <button type="submit" class="w-full py-2 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700">
                                Confirm Payment
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">History</h4>
                    <div class="space-y-3">
                        <?php if (empty($payments)): ?>
                            <p class="text-sm text-gray-400 text-center italic">No payments recorded yet.</p>
                        <?php else: ?>
                            <?php foreach ($payments as $pay): ?>
                            <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded border border-gray-100">
                                <div>
                                    <p class="font-medium text-gray-900 capitalize">
                                        <i class="fas fa-wallet text-gray-400 mr-1"></i> <?= $pay['payment_method'] ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= date('M j, H:i', strtotime($pay['created_at'])) ?></p>
                                    <?php if ($pay['transaction_id']): ?>
                                        <p class="text-xs text-gray-400">Ref: <?= e($pay['transaction_id']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="font-bold text-green-600"><?= format_currency($pay['amount']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Customer Details</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    <?= e($order['linked_customer_name'] ?? $order['customer_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500">Customer</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="break-all">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= e($order['linked_customer_email'] ?? $order['customer_email']) ?>
                                </p>
                                <p class="text-xs text-gray-500">Email</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    <?= e($order['linked_customer_phone'] ?? $order['customer_phone']) ?>
                                </p>
                                <p class="text-xs text-gray-500">Phone</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t">
                        <a href="<?= url('customers/edit/<?= $order['customer_id'] ?? '#' ?>') ?>" class="w-full block text-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            View Customer Profile
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>