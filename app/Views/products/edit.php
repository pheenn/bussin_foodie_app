<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4 flex justify-between items-center">
            <h1 class="pl-16 text-xl md:text-2xl font-bold text-gray-900">Edit Product</h1>
            <button type="button" onclick="confirmDelete()" class="md:hidden text-red-600 hover:text-red-800">
                <i class="fas fa-trash text-xl"></i>
            </button>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="<?= url('products/update/' . $product['id']) ?>" enctype="multipart/form-data">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="w-full md:w-1/3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                            <div class="relative group">
                                <img id="currentPreview" 
                                     src="<?= e($product['image_url']) ?>" 
                                     class="w-full h-48 object-cover rounded-lg border bg-gray-50"
                                     onerror="this.src='/assets/default.png'">
                                
                                <label class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer rounded-lg">
                                    <i class="fas fa-camera text-2xl mb-1"></i>
                                    <span class="text-sm">Change Image</span>
                                    <input type="file" name="image" accept="image/*" class="hidden" 
                                           onchange="document.getElementById('currentPreview').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                            <input type="text" name="image_url" value="<?= e($product['image_url']) ?>" class="mt-2 w-full text-xs text-gray-500 border rounded px-2 py-1" placeholder="Image URL">
                        </div>
                        
                        <div class="w-full md:w-2/3 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" value="<?= e($product['name']) ?>" required class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price</label>
                                    <input type="number" name="price" value="<?= e($product['price']) ?>" step="0.01" required class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select name="category_id" class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500">
                                        <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                                    <input type="number" name="stock_quantity" value="<?= e($product['stock_quantity']) ?>" class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Min Alert</label>
                                    <input type="number" name="min_stock" value="<?= e($product['min_stock']) ?>" class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?> class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                        <span class="ml-2 text-sm text-gray-700">Active (Visible to customers)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg mt-1 focus:ring-orange-500"><?= e($product['description']) ?></textarea>
                    </div>

                    <div class="flex flex-col-reverse md:flex-row justify-between pt-6 border-t gap-4">
                        <button type="button" onclick="confirmDelete()" class="hidden md:inline-flex px-6 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                            Delete Product
                        </button>
                        
                        <div class="flex space-x-3 justify-end w-full md:w-auto">
                            <a href="<?= url('products') ?>" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 shadow-sm">
                                Update Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <form id="deleteForm" action="<?= url('products/delete/' . $product['id']) ?>" method="POST" class="hidden">
                <?= CSRF::getTokenField() ?>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    Swal.fire({
        title: 'Delete Product?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    })
}
</script>