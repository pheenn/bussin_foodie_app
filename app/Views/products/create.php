<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="pl-16 text-xl md:text-2xl font-bold text-gray-900">Add New Product</h1>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="/products/store" enctype="multipart/form-data" id="productForm">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-orange-400 transition-colors text-center relative">
                        <label for="image" class="cursor-pointer block w-full">
                            <div id="uploadPlaceholder" class="py-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm font-medium text-gray-700">Click to upload product image</p>
                                <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB</p>
                            </div>
                            <img id="imagePreview" src="#" alt="Preview" class="hidden h-48 mx-auto rounded-lg shadow-sm object-contain">
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewFile(this)">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" name="price" step="0.01" required class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-orange-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Initial Stock</label>
                                <input type="number" name="stock_quantity" value="100" class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Min. Alert</label>
                                <input type="number" name="min_stock" value="10" class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500" title="Low stock alert threshold">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500"></textarea>
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-xs text-gray-500 mb-1">Or use Image URL (Optional)</label>
                        <input type="url" name="image_url" placeholder="https://..." class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-gray-50">
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="/products" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 shadow-sm">
                            Save Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewFile(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>