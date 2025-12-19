<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4 flex justify-between items-center">
            <h1 class="pl-16 text-xl font-bold text-gray-900">Edit Customer: <?= e($customer['name']) ?></h1>
            <button type="button" onclick="confirmDeleteCustomer(<?= $customer['id'] ?>, '<?= e($customer['name']) ?>')" class="md:hidden text-red-600 hover:text-red-800">
                <i class="fas fa-trash text-xl"></i>
            </button>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="<?= url('customers/update/' . $customer['id']) ?>">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="<?= e($customer['name']) ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required value="<?= e($customer['email']) ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="phone" value="<?= e($customer['phone']) ?>" placeholder="e.g., 0917-xxx-xxxx" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        <p class="mt-1 text-xs text-gray-500">Optional, but recommended for order tracking.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" rows="3" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500"><?= e($customer['address']) ?></textarea>
                    </div>
                    
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-500">Joined: <?= date('M j, Y h:i A', strtotime($customer['created_at'])) ?></p>
                        <?php if ($customer['updated_at']): ?>
                            <p class="text-xs text-gray-400">Last Updated: <?= date('M j, Y h:i A', strtotime($customer['updated_at'])) ?></p>
                        <?php endif; ?>
                    </div>


                    <div class="flex flex-col-reverse md:flex-row justify-between pt-6 border-t gap-4">
                        <button type="button" onclick="confirmDeleteCustomer(<?= $customer['id'] ?>, '<?= e($customer['name']) ?>')" class="hidden md:inline-flex px-6 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                            Delete Customer
                        </button>
                        
                        <div class="flex space-x-3 justify-end w-full md:w-auto">
                            <a href="<?= url('customers') ?>" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Update Customer</button>
                        </div>
                    </div>
                </div>
            </form>

            <form id="delete-customer-<?= $customer['id'] ?>" action="<?= url('customers/delete/' . $customer['id']) ?>" method="POST" class="hidden">
                <?= CSRF::getTokenField() ?>
            </form>
        </div>
    </div>
</div>

<script>
// Re-include the generic delete function from index.php if not globally available
function confirmDeleteCustomer(id, name) {
    Swal.fire({
        title: 'Delete Customer?',
        text: `Are you sure you want to delete customer: ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-customer-' + id).submit();
        }
    });
}
</script>