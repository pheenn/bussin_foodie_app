<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="text-xl font-bold text-gray-900">System Settings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage global store configurations</p>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="/settings/update">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-8">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">General Information</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-store text-gray-400"></i>
                                    </div>
                                    <input type="text" name="store_name" value="<?= e($settings['store_name'] ?? '') ?>" required 
                                           class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Contact Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Phone</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="text" name="store_phone" value="<?= e($settings['store_phone'] ?? '') ?>" 
                                           class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="store_email" value="<?= e($settings['store_email'] ?? '') ?>" 
                                           class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Financial</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                                <input type="text" name="currency" value="<?= e($settings['currency'] ?? 'PHP') ?>" 
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500 bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                                <p class="text-xs text-gray-500 mt-1">Currency format (e.g. PHP, USD)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (decimal)</label>
                                <input type="number" name="tax_rate" step="0.01" min="0" max="1" value="<?= e($settings['tax_rate'] ?? '0.12') ?>" 
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                <p class="text-xs text-gray-500 mt-1">Example: 0.12 for 12%</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                        <button type="button" onclick="window.history.back()" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 hover:to-red-600 shadow-md">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900">About Bussin' Foodie</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Current Version: <?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?><br>
                        Server Time: <?= date('Y-m-d H:i:s') ?><br>
                        PHP Version: <?= phpversion() ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>