<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 sm:px-6 lg:px-8 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="pl-16 text-xl md:text-2xl font-bold text-gray-900 truncate">Products</h1>
                    <p class="hidden md:block mt-1 text-sm text-gray-600">Manage your menu items, inventory, and pricing</p>
                </div>
                
                <div class="flex items-center space-x-3 ml-4">
                    <a href="<?= url('products/create') ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-medium rounded-lg hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Add Product</span>
                        <span class="sm:hidden">Add</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 mt-1"><?= $stats['total_products'] ?></p>
                    </div>
                    <div class="hidden md:flex w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg items-center justify-center">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Active</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 mt-1"><?= $stats['active_products'] ?></p>
                    </div>
                    <div class="hidden md:flex w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Low Stock</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 mt-1"><?= $stats['low_stock'] ?></p>
                    </div>
                    <div class="hidden md:flex w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Value</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 mt-1 tracking-tight"><?= format_currency($stats['total_value']) ?></p>
                    </div>
                    <div class="hidden md:flex w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg items-center justify-center">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6 md:mb-8" x-data="{ open: false }">
            <div class="md:hidden flex justify-between items-center mb-4" onclick="document.getElementById('filterContainer').classList.toggle('hidden')">
                <h3 class="font-medium text-gray-900">Filters & Search</h3>
                <i class="fas fa-chevron-down text-gray-500"></i>
            </div>
            
            <div id="filterContainer" class="hidden md:block">
                <form method="GET" action="<?= url('products') ?>" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="<?= e($search) ?>" 
                                       placeholder="Search products..."
                                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                <option value="all">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $selected_category == $category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                            <select name="stock_filter" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                <option value="">All Stock Status</option>
                                <option value="in_stock" <?= $selected_stock_filter == 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                                <option value="low_stock" <?= $selected_stock_filter == 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
                                <option value="out_of_stock" <?= $selected_stock_filter == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
                        <div class="w-full sm:w-auto">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium">
                                <i class="fas fa-filter mr-2"></i> Apply Filters
                            </button>
                            <?php if ($search || $selected_category || $selected_stock_filter): ?>
                            <a href="<?= url('products') ?>" class="mt-2 sm:mt-0 sm:ml-3 block sm:inline-block text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                Clear
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-sm text-gray-500 hidden sm:block">
                            <?= count($products) ?> items found
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 md:h-12 md:w-12">
                                                <img class="h-10 w-10 md:h-12 md:w-12 rounded-lg object-cover" 
                                                     src="<?= !empty($product['image_url']) ? e($product['image_url']) : '/assets/default-product.png' ?>" 
                                                     alt="<?= e($product['name']) ?>"
                                                     onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                            </div>
                                            <div class="ml-3 md:ml-4">
                                                <div class="text-sm font-medium text-gray-900 line-clamp-1">
                                                    <?= e($product['name']) ?>
                                                </div>
                                                <div class="text-xs text-gray-500 md:hidden">
                                                    <?= e($product['category_name']) ?> • Stock: <?= $product['stock_quantity'] ?>
                                                </div>
                                                <div class="hidden md:block text-xs text-gray-500 line-clamp-1">
                                                    <?= e($product['description']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= get_category_color($product['category_id']) ?> text-white">
                                            <?= e($product['category_name']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= format_currency($product['price']) ?>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $product['stock_quantity'] ?></div>
                                        <?php if ($product['stock_status'] == 'low_stock'): ?>
                                            <span class="text-xs text-red-600 font-medium">Low Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" 
                                                    class="text-green-600 hover:text-green-900"
                                                    onclick="showStockModal(<?= $product['id'] ?>, '<?= e($product['name']) ?>', <?= $product['stock_quantity'] ?>)">
                                                <i class="fas fa-box"></i>
                                            </button>
                                            
                                            <a href="<?= url('products/edit/' . $product['id']) ?>" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="confirmDeleteProduct(<?= $product['id'] ?>, '<?= e($product['name']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-<?= $product['id'] ?>" 
                                              action="<?= url('products/delete/' . $product['id']) ?>" 
                                              method="POST" 
                                              class="hidden">
                                            <?= CSRF::getTokenField() ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No products found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once VIEWS_PATH . '/products/_stock_modal.php'; ?>

<script>
// SweetAlert Delete Confirmation
function confirmDeleteProduct(id, name) {
    Swal.fire({
        title: 'Delete Product?',
        text: `Are you sure you want to delete "${name}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Ensure Stock Modal Logic (Moved to separate file or inline here for brevity)
function showStockModal(productId, productName, currentStock) {
    // ... existing stock modal logic ...
    const modal = document.getElementById('stockModal');
    if(modal) {
        document.getElementById('stockProductId').value = productId;
        document.getElementById('stockModalTitle').textContent = 'Update Stock: ' + productName;
        document.getElementById('currentStock').value = currentStock;
        document.getElementById('newStock').value = currentStock;
        document.getElementById('stockUpdateForm').action = '<?= url('products/update-stock') ?>?id=' + productId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function hideStockModal() {
    const modal = document.getElementById('stockModal');
    if(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>