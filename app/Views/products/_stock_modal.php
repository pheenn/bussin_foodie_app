<div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full transform transition-all">
        <div class="p-6">
            <h3 class="pl-16 text-lg font-semibold text-gray-900 mb-4" id="stockModalTitle">Update Stock</h3>
            <form id="stockUpdateForm" method="POST" action="">
                <?= CSRF::getTokenField() ?>
                <input type="hidden" name="product_id" id="stockProductId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                        <input type="number" 
                               id="currentStock" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500" 
                               disabled>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Stock Quantity</label>
                        <input type="number" 
                               name="stock_quantity" 
                               id="newStock" 
                               min="0" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select name="stock_action" 
                                id="stockAction"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="set">Set to this amount</option>
                            <option value="add">Add to current stock</option>
                            <option value="subtract">Subtract from current stock</option>
                        </select>
                    </div>
                    
                    <div id="stockResult" class="hidden p-3 bg-gray-100 rounded-lg">
                        <p class="text-sm text-gray-700"><span id="resultText"></span> will be: <span id="resultAmount" class="font-bold text-gray-900"></span></p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideStockModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Logic
document.addEventListener('DOMContentLoaded', function() {
    // Live calculation for stock preview
    const newStockInput = document.getElementById('newStock');
    const stockActionSelect = document.getElementById('stockAction');
    
    if (newStockInput && stockActionSelect) {
        const updatePreview = () => {
            const current = parseInt(document.getElementById('currentStock').value) || 0;
            const inputVal = parseInt(newStockInput.value) || 0;
            const action = stockActionSelect.value;
            
            let result = current;
            let text = '';
            
            switch(action) {
                case 'set': 
                    result = inputVal; 
                    text = 'New stock';
                    break;
                case 'add': 
                    result = current + inputVal; 
                    text = 'Stock after adding';
                    break;
                case 'subtract': 
                    result = Math.max(0, current - inputVal); 
                    text = 'Stock after subtracting';
                    break;
            }
            
            document.getElementById('resultText').textContent = text;
            document.getElementById('resultAmount').textContent = result;
            document.getElementById('stockResult').classList.remove('hidden');
        };

        newStockInput.addEventListener('input', updatePreview);
        stockActionSelect.addEventListener('change', updatePreview);
    }
});
</script>