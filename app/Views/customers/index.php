<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="pl-16 text-xl md:text-2xl font-bold text-gray-900">Customer Management</h1>
                    <p class="hidden md:block mt-1 text-sm text-gray-600">View and manage customer details and profiles</p>
                </div>
                <div class="flex items-center space-x-3 ml-4">
                    <a href="<?= url('customers/create') ?>" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-medium rounded-lg hover:from-orange-600 hover:to-red-600 shadow-sm transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Add Customer
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $c): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= e($c['name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= e($c['email']) ?></div>
                                <?php if (!empty($c['phone'])): ?>
                                    <div class="text-xs text-gray-400 mt-1"><?= e($c['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4 text-sm text-gray-500 max-w-sm truncate" title="<?= e($c['address']) ?>">
                                <?= e(empty($c['address']) ? 'N/A' : $c['address']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M j, Y', strtotime($c['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="<?= url('customers/edit/<?= $c['id'] ?>') ?>" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></a>
                                    
                                    <button onclick="confirmDeleteCustomer(<?= $c['id'] ?>, '<?= e($c['name']) ?>')" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-customer-<?= $c['id'] ?>" action="<?= url('customers/delete/<?= $c['id'] ?>') ?>" method="POST" class="hidden">
                                        <?= CSRF::getTokenField() ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-user-plus text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">No customers found.</p>
                                    <p class="text-sm mt-1">Add your first customer to get started.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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