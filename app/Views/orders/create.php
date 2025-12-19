<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="pl-16 text-xl font-bold text-gray-900">Create New Order</h1>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-5xl mx-auto">
        <form method="POST" action="<?= url('orders/store') ?>" id="orderForm">
            <?= CSRF::getTokenField() ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                            <button type="button" onclick="addItemRow()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                + Add Item
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full" id="itemsTable">
                                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                    <tr>
                                        <th class="p-3 w-1/2">Product</th>
                                        <th class="p-3 w-24">Price</th>
                                        <th class="p-3 w-24">Qty</th>
                                        <th class="p-3 w-24 text-right">Total</th>
                                        <th class="p-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="itemsContainer"></tbody>
                                <tfoot class="border-t">
                                    <tr>
                                        <td colspan="3" class="p-3 text-right font-bold text-gray-700">Grand Total:</td>
                                        <td class="p-3 text-right font-bold text-xl text-orange-600" id="grandTotal">₱0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="emptyMessage" class="text-center py-8 text-gray-500 text-sm">
                            No items added. Click "Add Item" to start.
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500" placeholder="Special instructions..."></textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Customer</label>
                                <select name="customer_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500">
                                    <option value="">-- Choose --</option>
                                    <?php foreach ($customers as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['phone']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seat / Table Number</label>
                                <input type="text" name="seat_number" maxlength="10" placeholder="e.g., T-12 or A1" 
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 uppercase">
                                <p class="text-xs text-gray-500 mt-1">Max 10 characters</p>
                            </div>

                            <div class="pt-2 text-center">
                                <a href="<?= url('customers/create') ?>" target="_blank" class="text-sm text-blue-600 hover:underline">Register New Customer</a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-lg hover:from-orange-600 shadow-md">
                            Create Order
                        </button>
                        <a href="<?= url('orders') ?>" class="w-full py-3 text-center border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const products = <?= json_encode($products) ?>;
</script>
<script>
function formatCurrency(amount) { return '₱' + parseFloat(amount).toFixed(2); }

function addItemRow() {
    const container = document.getElementById('itemsContainer');
    const index = container.children.length;
    
    let options = '<option value="">Select Product</option>';
    products.forEach(p => { 
        // Add stock info to label
        options += `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock_quantity}">${p.name} (Stock: ${p.stock_quantity})</option>`; 
    });

    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="p-2">
            <select name="items[${index}][product_id]" class="w-full border rounded p-1 text-sm product-select" required onchange="updateRow(this)">${options}</select>
        </td>
        <td class="p-2 text-sm text-gray-600 price-display">₱0.00</td>
        <td class="p-2"><input type="number" name="items[${index}][quantity]" value="1" min="1" class="w-full border rounded p-1 text-center quantity-input" required oninput="updateRow(this)"></td>
        <td class="p-2 text-right text-sm font-medium total-display">₱0.00</td>
        <td class="p-2 text-center"><button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button></td>
    `;
    
    container.appendChild(row);
    document.getElementById('emptyMessage').classList.add('hidden');
}

function updateRow(element) {
    const row = element.closest('tr');
    const select = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.quantity-input');
    
    const option = select.options[select.selectedIndex];
    const price = parseFloat(option?.dataset.price || 0);
    const stock = parseInt(option?.dataset.stock || 0);
    let qty = parseInt(qtyInput.value || 0);

    // Simple JS stock check
    if(qty > stock) {
        alert('Quantity exceeds available stock (' + stock + ')');
        qty = stock;
        qtyInput.value = stock;
    }

    const total = price * qty;

    row.querySelector('.price-display').textContent = formatCurrency(price);
    row.querySelector('.total-display').textContent = formatCurrency(total);
    
    calculateGrandTotal();
}

function removeRow(btn) {
    btn.closest('tr').remove();
    if(document.getElementById('itemsContainer').children.length === 0) {
        document.getElementById('emptyMessage').classList.remove('hidden');
    }
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('#itemsContainer tr').forEach(row => {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.quantity-input').value;
        const price = parseFloat(select.options[select.selectedIndex]?.dataset.price || 0);
        grandTotal += (price * qty);
    });
    document.getElementById('grandTotal').textContent = formatCurrency(grandTotal);
}

document.addEventListener('DOMContentLoaded', () => addItemRow());
</script>