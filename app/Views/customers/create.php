<div class="min-h-full-screen">
    <header class="bg-white shadow md:shadow-sm sticky top-0 z-20">
        <div class="px-4 py-4">
            <h1 class="pl-16 text-xl font-bold text-gray-900">Add New Customer</h1>
        </div>
    </header>
    
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="/customers/store">
                <?= CSRF::getTokenField() ?>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="<?= e($_SESSION['old_input']['name'] ?? '') ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required value="<?= e($_SESSION['old_input']['email'] ?? '') ?>" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="phone" value="<?= e($_SESSION['old_input']['phone'] ?? '') ?>" placeholder="e.g., 0917-xxx-xxxx" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500">
                        <p class="mt-1 text-xs text-gray-500">Optional, but recommended for order tracking.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" rows="3" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-orange-500"><?= e($_SESSION['old_input']['address'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="/customers" class="px-6 py-2 border rounded-lg hover:bg-gray-50 text-gray-700">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Save Customer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php unset($_SESSION['old_input']); ?>