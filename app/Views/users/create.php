<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="text-xl font-bold text-gray-900">Create New User</h1>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="/users/store">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="full_name" required value="<?= e($_SESSION['old_input']['full_name'] ?? '') ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" required value="<?= e($_SESSION['old_input']['username'] ?? '') ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" required value="<?= e($_SESSION['old_input']['email'] ?? '') ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                            <option value="admin">Administrator (Full Access)</option>
                            <option value="staff">Staff (Limited Access)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="confirm_password" required class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="/users" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php unset($_SESSION['old_input']); ?>