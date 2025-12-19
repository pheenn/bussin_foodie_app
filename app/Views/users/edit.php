<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="text-xl font-bold text-gray-900">Edit User</h1>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="<?= url('users/update/<?= $user['id'] ?>') ?>">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" value="<?= e($user['username']) ?>" required class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" value="<?= e($user['email']) ?>" required class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                            <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                        </select>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mt-4">
                        <h3 class="text-sm font-medium text-yellow-800 mb-2">Change Password</h3>
                        <p class="text-xs text-yellow-600 mb-4">Leave these fields blank if you don't want to change the password.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">New Password</label>
                                <input type="password" name="password" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="<?= url('users') ?>" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>