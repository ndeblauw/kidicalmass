<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-kidical-blue dark:text-kidical-yellow mb-6">Contact Us</h2>
    
    @if($submitted)
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded mb-4">
            <p class="font-medium">Thank you for your message!</p>
            <p class="text-sm">We'll get back to you as soon as possible.</p>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6 grid grid-cols-2 gap-6">
        <div class="space-y-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-kidical-orange focus:border-transparent dark:bg-gray-700 dark:text-white"
                    required
                >
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-kidical-orange focus:border-transparent dark:bg-gray-700 dark:text-white"
                    required
                >
                @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Field (Optional) -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone
                </label>
                <input
                    type="tel"
                    id="phone"
                    wire:model="phone"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-kidical-orange focus:border-transparent dark:bg-gray-700 dark:text-white"
                >
                @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Message Field -->
        <div>
            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Message <span class="text-red-500">*</span>
            </label>
            <textarea
                id="message"
                wire:model="message"
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-kidical-orange focus:border-transparent dark:bg-gray-700 dark:text-white"
                required
            ></textarea>
            @error('message')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Honeypot Field (Hidden) -->
        <div class="hidden" aria-hidden="true">
            <label for="website">Website</label>
            <input
                type="text"
                id="website"
                wire:model="website"
                tabindex="-1"
                autocomplete="off"
            >
        </div>

        <!-- Submit Button -->
        <div class="col-span-2">
            <button
                type="submit"
                class="w-full px-6 py-3 bg-kidical-green hover:bg-kidical-blue text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-kidical-orange focus:ring-offset-2"
            >
                Send Message
            </button>
        </div>
    </form>
</div>
