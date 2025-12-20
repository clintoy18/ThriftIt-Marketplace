<div x-data="{ 
        open: false, 
        items: [{ id: Date.now() }],
        avatarPreview: null,
        itemPreviews: {}
     }" 
     @open-buyer-modal.window="open = true; items = [{ id: Date.now() }]; avatarPreview = null; itemPreviews = {}" 
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;"
     x-cloak>
    
    {{-- Flex container for perfect centering --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        
        {{-- Background Overlay --}}
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" 
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false; items = [{ id: Date.now() }]; avatarPreview = null; itemPreviews = {}"></div>

        {{-- Modal Content Card --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all w-full max-w-2xl mx-auto overflow-hidden"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <form action="{{ route('featured-buyers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Feature a Celebrity Buyer</h3>
                    <button type="button" @click="open = false; items = [{ id: Date.now() }]; avatarPreview = null; itemPreviews = {}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    {{-- Buyer Profile Section --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Celebrity Name</label>
                            <input type="text" name="name" required placeholder="e.g. Alexa Rivera" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Social Handle</label>
                            <input type="text" name="handle" placeholder="@username" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profile Photo (Avatar)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 dark:border-gray-700 border-dashed rounded-xl hover:border-indigo-400 transition-colors relative">
                                <div class="space-y-1 text-center" x-show="!avatarPreview">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label class="relative cursor-pointer bg-transparent rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input type="file" name="avatar" accept="image/*" @change="avatarPreview = URL.createObjectURL($event.target.files[0])" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                                <div x-show="avatarPreview" class="w-full">
                                    <img :src="avatarPreview" alt="Avatar preview" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                    <button type="button" @click="avatarPreview = null; $el.closest('form').querySelector('input[name=avatar]').value = ''" class="mt-2 text-xs text-red-600 hover:text-red-700">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items Section --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-widest">Purchased Items</h4>
                            <button type="button" @click="items.push({ id: Date.now() })" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-600 px-3 py-1.5 rounded-full transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                Add Item
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="item.id">
                                <div class="bg-gray-50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 relative group">
                                    <button type="button" @click="delete itemPreviews[item.id]; items = items.filter(i => i.id !== item.id)" 
                                            class="absolute -top-2 -right-2 bg-white dark:bg-gray-800 text-gray-400 hover:text-red-500 shadow-md border border-gray-100 dark:border-gray-700 rounded-full p-1 transition-colors" 
                                            x-show="items.length > 1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2 space-y-1">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase">Item Name</label>
                                            <input type="text" :name="`items[${index}][product_name]`" required placeholder="Designer Trench Coat..." class="w-full text-sm rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase">Price (₱)</label>
                                            <input type="number" :name="`items[${index}][price]`" required placeholder="0.00" class="w-full text-sm rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase">Item Photo</label>
                                            <div class="space-y-2">
                                                <input type="file" 
                                                       :name="`items[${index}][image]`" 
                                                       accept="image/*" 
                                                       @change="itemPreviews[item.id] = URL.createObjectURL($event.target.files[0])"
                                                       required 
                                                       class="block w-full text-xs text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                                                <div x-show="itemPreviews[item.id]" class="mt-2">
                                                    <img :src="itemPreviews[item.id]" alt="Item preview" class="h-20 w-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                                                    <button type="button" 
                                                            @click="delete itemPreviews[item.id]; $el.closest('div').querySelector('input[type=file]').value = ''" 
                                                            class="mt-1 text-[10px] text-red-600 hover:text-red-700">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <button type="button" @click="open = false; items = [{ id: Date.now() }]; avatarPreview = null; itemPreviews = {}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>