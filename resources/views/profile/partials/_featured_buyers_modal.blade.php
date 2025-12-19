<div x-data="{ 
        open: false, 
        items: [{ id: Date.now() }] 
     }" 
     @open-buyer-modal.window="open = true" 
     x-show="open" 
     class="fixed inset-0 z-[60] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background Overlay --}}
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="open = false"></div>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
           <form action="{{ route('featured-buyers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Feature a Celebrity Buyer</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Buyer Profile Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Celebrity Name</label>
                        <input type="text" name="name" required placeholder="e.g. Alexa Rivera" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Social Handle</label>
                        <input type="text" name="handle" placeholder="@username" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Profile Photo (Avatar)</label>
                        <input type="file" name="avatar" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-gray-700 dark:file:text-indigo-300">
                    </div>
                </div>

                {{-- Purchased Items Section --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Purchased Items</h4>
                        <button type="button" @click="items.push({ id: Date.now() })" class="text-xs font-bold text-indigo-600 hover:text-indigo-500 flex items-center gap-1 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Another Item
                        </button>
                    </div>

                    <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl mb-4 relative border border-gray-100 dark:border-gray-700">
                                {{-- Remove Item Button --}}
                                <button type="button" @click="items = items.filter(i => i.id !== item.id)" 
                                        class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" 
                                        x-show="items.length > 1">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="md:col-span-2">
                                        <input type="text" :name="`items[${index}][product_name]`" required placeholder="Product Name (e.g. Designer Trench Coat)" class="block w-full text-xs rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <input type="number" :name="`items[${index}][price]`" required placeholder="Price (₱)" class="block w-full text-xs rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <input type="file" :name="`items[${index}][image]`" required class="block w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-indigo-50 dark:file:bg-gray-700">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-all flex items-center">
                        Save Featured Buyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>