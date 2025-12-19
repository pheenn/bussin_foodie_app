<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">User Management</h1>
                    <p class="hidden md:block mt-1 text-sm text-gray-600">Manage system administrators and staff access</p>
                </div>
                <div class="flex items-center space-x-3 ml-4">
                    <a href="<?= url('users/create') ?>" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-medium rounded-lg hover:from-orange-600 hover:to-red-600 shadow-sm transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Add User
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                        <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= e($u['full_name']) ?></div>
                                        <div class="text-sm text-gray-500">@<?= e($u['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $u['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= e($u['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M j, Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="<?= url('users/edit/' . $u['id']) ?>" class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></a>
                                    
                                    <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                                        <button onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= e($u['username']) ?>')" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-user-<?= $u['id'] ?>" action="<?= url('users/delete/' . $u['id']) ?>" method="POST" class="hidden">
                                            <?= CSRF::getTokenField() ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteUser(id, name) {
    Swal.fire({
        title: 'Delete User?',
        text: `Are you sure you want to delete ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-user-' + id).submit();
        }
    });
}
</script>