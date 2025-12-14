@php
    // Prepare existing images from session (e.g. if validation fails and page reloads)
    $existingImages = [];
    if (session()->has('product_images')) {
        foreach (session('product_images') as $path) {
            if (!empty($path) && is_string($path) && trim($path) !== '') {
                try {
                    $url = Storage::disk('s3')->url(trim($path));
                    $existingImages[] = [
                        'path' => $path,
                        'url' => $url,
                        'name' => basename($path),
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
@endphp

<x-app-layout>
    <div class="pt-8 sm:pt-12 pb-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden lg:overflow-visible">

            {{-- Mobile Header --}}
            <div class="block md:hidden mb-8">
                <h2 class="text-xl font-bold text-custom-dark text-center">
                    <i>Sell <img src="{{ asset('images/image 165.png') }}" alt="emoji"
                            class="inline-block h-5 w-4 align-middle ml-1"></i>
                </h2>
                <hr class="w-full mt-4 h-px bg-gray-800 border-0 dark:bg-gray-700">
            </div>

            {{-- Verification Status --}}
            @if(auth()->user()->is_verified)
                <x-step-progress :currentStep="$currentStep" />
            @else
                <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20 px-4 py-3 flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-800/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-700 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.1 19h13.8A2.1 2.1 0 0021 16.9V7.1A2.1 2.1 0 0018.9 5H5.1A2.1 2.1 0 003 7.1v9.8A2.1 2.1 0 005.1 19z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">Not verified</p>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm text-amber-700 dark:text-amber-300">Please verify your account to enable the selling steps.</p>
                            <a href="{{ route('profile.edit') ?? url('/email/verify') }}"
                               class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline hover:text-blue-700 dark:hover:text-blue-300 transition whitespace-nowrap">
                                Click here to verify
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <form id="productForm" action="{{ route('sell-item.store-step1') }}" method="POST"
                enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Hidden Container for Removed Images --}}
                <div id="removedImagesContainer"></div>

                {{-- GRID LAYOUT --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-10 items-start lg:relative lg:left-[-150px]">

                    {{-- LEFT COLUMN (Photos) --}}
                    <div class="lg:col-span-2 flex flex-col w-full lg:w-[450px]">
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Photos</h3>

                            {{-- Photo Guidelines --}}
                            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                    </svg>
                                    Photo Guidelines
                                </h4>
                                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                    <li class="flex items-start gap-2"><span
                                            class="text-[#B59F84] mt-0.5">•</span><span><strong>Cover Photo:</strong>
                                            Main product shot</span></li>
                                    <li class="flex items-start gap-2"><span
                                            class="text-[#B59F84] mt-0.5">•</span><span><strong>Details:</strong> Show
                                            any flaws</span></li>
                                </ul>
                            </div>

                            {{-- Drop Zone --}}
                            <div class="mb-4 mt-6 lg:mt-[30px]">
                                <label for="productImages" id="productDropZone"
                                    class="upload-tile group cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-300/80 rounded-3xl transition-all duration-500 hover:border-primary-400 hover:shadow-xl bg-white/80 hover:bg-white backdrop-blur-sm p-8 min-h-[192px]">

                                    {{-- PREVIEW CONTAINER --}}
                                    <div id="allPreviews" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4 w-full">
                                        {{-- Existing images loop --}}
                                        @foreach ($existingImages as $index => $img)
                                            <div id="existing-{{ $index }}"
                                                class="preview-item existing-item relative h-24 rounded-lg overflow-hidden border border-gray-200">
                                                <img src="{{ $img['url'] }}" class="w-full h-full object-cover">
                                                <button type="button"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition-colors z-20"
                                                    onclick="removeExistingImage('{{ $img['path'] }}', 'existing-{{ $index }}')">×</button>
                                                <span
                                                    class="preview-number absolute top-1 left-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">{{ $index + 1 }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div id="donationAddMoreText"
                                        class="text-center mb-4 {{ count($existingImages) > 0 ? '' : 'hidden' }}">
                                        <p class="text-sm text-[#B59F84] font-medium">Tap to add more photos</p>
                                    </div>

                                    <div id="dropZoneContent"
                                        class="flex flex-col items-center justify-center gap-5 w-full {{ count($existingImages) > 0 ? 'hidden' : '' }}">
                                        <div class="flex justify-center w-full">
                                            <div
                                                class="shrink-0 w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-9 sm:w-9"
                                                    viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M3 5a2 2 0 0 1 2-2h3l2 2h6a2 2 0 0 1 2 2v2H3V5Zm0 6h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Zm9 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-center justify-center gap-4 text-center">
                                            <span
                                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-full bg-gradient-to-r from-[#E1D5B6] to-[#d4c6a2] text-[#6f5e49]">Browse
                                                files</span>
                                            <div class="flex flex-col gap-2">
                                                <p
                                                    class="text-sm font-semibold text-gray-700 bg-gray-100/50 px-3 py-1.5 rounded-lg">
                                                    PNG or JPG up to 5MB</p>
                                                <span class="text-sm text-gray-600 font-medium">Add 2 to 8 photos</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <input id="productImages" name="images[]" type="file" accept="image/*" multiple
                                class="hidden">
                            
                            {{-- Validation Errors for Images --}}
                            <p id="productImageError" class="mt-2 text-sm text-red-600 hidden"></p>
                            <p id="productReachLimitError" class="mt-2 text-sm text-red-600 hidden">You can only upload up to 8 photos.</p>
                            @error('images')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('images.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- RIGHT COLUMN (Form Details) --}}
                    <div class="lg:col-span-3 flex lg:justify-end lg:relative lg:left-[250px] w-full lg:w-[640px]">

                        <div class="bg-[#F4F2ED] dark:bg-gray-800 shadow-lg rounded-lg overflow-visible w-full lg:w-[680px] ml-auto">
                            <div class="p-4 sm:p-6">
                                <div class="space-y-6">
                                    
                                    {{-- Header --}}
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Step 1: Item Details</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Essential information about your item.</p>
                                        <div class="h-px w-full bg-gray-200 dark:bg-gray-700 mt-4"></div>
                                    </div>

                                    {{-- HIDDEN INPUTS (Preserving required backend data) --}}
                                    <input type="hidden" name="condition" value="{{ old('condition', session('product_step1.condition')) ?? 'used' }}">
                                    <input type="hidden" name="status" value="available">

                                    {{-- 1. ITEM NAME (Full Width) --}}
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Item Name <span class="ml-1 text-red-500">*</span></label>
                                        <input type="text" id="name" name="name"
                                            class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                            placeholder="e.g., Vintage Denim Jacket"
                                            value="{{ old('name', session('product_step1.name')) }}" required>
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- 2. CATEGORY & SIZE (Grid: Category First, then Size) --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                        {{-- Category --}}
                                        <div>
                                            <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Category <span class="ml-1 text-red-500">*</span></label>
                                            <select id="category_id" name="category_id"
                                                class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                                required>
                                                <option value="" disabled selected>Select category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', session('product_step1.category_id')) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Size --}}
                                        <div>
                                            <label for="size" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Size <span class="ml-1 text-red-500">*</span></label>
                                            <select id="size" name="size"
                                                class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                                required>
                                                <option value="" disabled {{ old('size', session('product_step1.size')) ? '' : 'selected' }}>Select size</option>
                                                {{-- Options grouped carefully for JS logic --}}
                                                <optgroup label="Clothing">
                                                    <option value="XS">XS</option>
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXL">XXL</option>
                                                </optgroup>
                                                <optgroup label="Shoes">
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </optgroup>
                                                <optgroup label="Accessories">
                                                    <option value="One Size">One Size</option>
                                                </optgroup>
                                                <optgroup label="Socks / Hosiery">
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                </optgroup>
                                            </select>
                                            @error('size')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 3. PRICE & TARGET AUDIENCE (Grid) --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                        {{-- Price --}}
                                        <div>
                                            <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Price (PHP) <span class="ml-1 text-red-500">*</span></label>
                                            <div class="relative mt-1">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                                </div>
                                                <input type="number" step="0.01" id="price" name="price"
                                                    class="block w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                                    placeholder="0.00"
                                                    value="{{ old('price', session('product_step1.price')) }}" required>
                                            </div>
                                            @error('price')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Target Audience --}}
                                        <div>
                                            <label for="segment_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Target Audience <span class="ml-1 text-red-500">*</span></label>
                                            <select id="segment_id" name="segment_id"
                                                class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                                required>
                                                <option value="" disabled selected>Select segment</option>
                                                @foreach ($segments as $segment)
                                                    <option value="{{ $segment->id }}"
                                                        {{ old('segment_id', session('product_step1.segment_id')) == $segment->id ? 'selected' : '' }}>
                                                        {{ ucfirst($segment->name) }}</option>
                                                @endforeach
                                            </select>
                                            @error('segment_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 4. BARANGAY (Full Width) --}}
                                    <div>
                                        <label for="barangay_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Barangay <span class="ml-1 text-red-500">*</span></label>
                                        <select id="barangay_id" name="barangay_id"
                                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                                            required>
                                            <option value="" disabled selected>Select a barangay</option>
                                            @foreach ($barangays as $barangay)
                                                <option value="{{ $barangay->id }}"
                                                    {{ old('barangay_id', session('product_step1.barangay_id')) == $barangay->id ? 'selected' : '' }}>
                                                    {{ $barangay->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('barangay_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- 5. DESCRIPTION (Full Width) --}}
                                    <div>
                                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Description <span class="ml-1 text-red-500">*</span></label>
                                        <textarea id="description" name="description" rows="5"
                                            class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm resize-none"
                                            placeholder="Tell us about the condition, brand, or story of your item..." required>{{ old('description', session('product_step1.description')) }}</textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- SUBMIT BUTTON --}}
                                    <div class="flex justify-center sm:justify-end pt-4 pb-2">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center bg-[#B59F84] text-white px-8 sm:px-10 py-3 rounded-[10px] text-sm sm:text-base font-semibold hover:bg-[#a08e77] transform hover:scale-105 transition-all duration-300 shadow-md w-full sm:w-auto">
                                            @if (auth()->user()->is_verified)
                                                Next: Upload QR Code &rarr;
                                            @else
                                                Review & Publish Item &rarr;
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // GLOBAL FUNCTION: Handle deletion of Existing (Session) images
        window.removeExistingImage = function(path, elementId) {
            const el = document.getElementById(elementId);
            if (el) el.remove();

            const container = document.getElementById('removedImagesContainer');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'removed_images[]';
            input.value = path;
            container.appendChild(input);

            document.dispatchEvent(new Event('existingImageRemoved'));
        };

        document.addEventListener('DOMContentLoaded', function() {
            // --- IMAGE UPLOAD LOGIC ---
            const input = document.getElementById('productImages');
            const allPreviewsContainer = document.getElementById('allPreviews');
            const dropZone = document.getElementById('productDropZone');
            const addMoreText = document.getElementById('donationAddMoreText');
            const dropZoneContent = document.getElementById('dropZoneContent');
            const errorEl = document.getElementById('productImageError');
            const reachLimitEl = document.getElementById('productReachLimitError');
            const form = document.getElementById('productForm');

            let newFiles = [];

            function getVisibleCount() {
                return document.querySelectorAll('.preview-item').length;
            }

            function updateUI() {
                const count = getVisibleCount();
                if (count > 0) {
                    dropZoneContent.classList.add('hidden');
                    addMoreText.classList.remove('hidden');
                    dropZone.style.minHeight = 'auto';
                } else {
                    dropZoneContent.classList.remove('hidden');
                    addMoreText.classList.add('hidden');
                    dropZone.style.minHeight = '192px';
                }
                document.querySelectorAll('.preview-number').forEach((badge, idx) => {
                    badge.textContent = idx + 1;
                });
                if (count >= 8) {
                    addMoreText.classList.add('hidden');
                }
            }

            document.addEventListener('existingImageRemoved', updateUI);

            input.addEventListener('change', () => {
                const files = Array.from(input.files || []);
                if (errorEl) errorEl.classList.add('hidden');
                files.forEach(file => {
                    if (getVisibleCount() < 8) {
                        newFiles.push(file);
                        renderNewFile(file);
                    } else {
                        if (reachLimitEl) {
                            reachLimitEl.classList.remove('hidden');
                            setTimeout(() => reachLimitEl.classList.add('hidden'), 2500);
                        }
                    }
                });
                syncInput();
                updateUI();
            });

            function renderNewFile(file) {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-item new-preview-item relative h-24 rounded-lg overflow-hidden border border-gray-200';
                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';
                const removeBtn = document.createElement('span');
                removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs cursor-pointer hover:bg-red-600 transition-colors z-20';
                removeBtn.textContent = '×';
                const badge = document.createElement('span');
                badge.className = 'preview-number absolute top-1 left-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';

                removeBtn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const idx = newFiles.indexOf(file);
                    if (idx > -1) newFiles.splice(idx, 1);
                    wrapper.remove();
                    syncInput();
                    updateUI();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(badge);
                wrapper.appendChild(removeBtn);
                allPreviewsContainer.appendChild(wrapper);

                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(file);
            }

            function syncInput() {
                const dt = new DataTransfer();
                newFiles.forEach(f => dt.items.add(f));
                input.files = dt.files;
            }

            form.addEventListener('submit', (e) => {
                if (getVisibleCount() < 2) {
                    e.preventDefault();
                    if (errorEl) {
                        errorEl.textContent = 'Please upload at least 2 photos.';
                        errorEl.classList.remove('hidden');
                    }
                }
            });

            updateUI();

            if (dropZone) {
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (getVisibleCount() < 8) dropZone.classList.add('ring-2', 'ring-blue-400');
                    else dropZone.classList.add('ring-2', 'ring-red-400');
                });
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('ring-2', 'ring-blue-400', 'ring-red-400');
                });
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('ring-2', 'ring-blue-400', 'ring-red-400');
                    if (getVisibleCount() >= 8) {
                        if (reachLimitEl) {
                            reachLimitEl.classList.remove('hidden');
                            setTimeout(() => reachLimitEl.classList.add('hidden'), 2500);
                        }
                        return;
                    }
                    const files = Array.from(e.dataTransfer.files || []);
                    let added = false;
                    for (const f of files) {
                        if (getVisibleCount() < 8) {
                            newFiles.push(f);
                            renderNewFile(f);
                            added = true;
                        }
                    }
                    if (added) {
                        syncInput();
                        updateUI();
                    }
                });
                dropZone.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-btn') || e.target.tagName === 'BUTTON') {
                        e.preventDefault();
                        return;
                    }
                    if (getVisibleCount() >= 8) {
                        e.preventDefault();
                        if (reachLimitEl) {
                            reachLimitEl.classList.remove('hidden');
                            setTimeout(() => reachLimitEl.classList.add('hidden'), 2500);
                        }
                    }
                });
            }
        });

        // --- DYNAMIC SIZE LOGIC ---
        (function() {
            let sizeTemplates = null;

            function initSizeTemplates() {
                const sizeSelect = document.getElementById('size');
                if (!sizeSelect || sizeTemplates) return;

                const placeholder = sizeSelect.querySelector('option[value=""]');
                
                // Matches standard optgroup labels used in HTML
                const clothingGroup = sizeSelect.querySelector('optgroup[label="Clothing"]');
                const shoesGroup = sizeSelect.querySelector('optgroup[label="Shoes"]');
                const accessoriesGroup = sizeSelect.querySelector('optgroup[label="Accessories"]');
                const socksGroup = sizeSelect.querySelector('optgroup[label="Socks / Hosiery"]');

                sizeTemplates = {
                    placeholder: placeholder ? placeholder.outerHTML : '',
                    clothing: clothingGroup ? clothingGroup.outerHTML : '',
                    shoes: shoesGroup ? shoesGroup.outerHTML : '',
                    accessories: accessoriesGroup ? accessoriesGroup.outerHTML : '',
                    socks: socksGroup ? socksGroup.outerHTML : '',
                    full: sizeSelect.innerHTML
                };
            }

            function updateSizeOptions() {
                const categorySelect = document.getElementById('category_id');
                const sizeSelect = document.getElementById('size');
                if (!categorySelect || !sizeSelect) return;

                initSizeTemplates();
                if (!sizeTemplates) return;

                const selectedIndex = categorySelect.selectedIndex;
                // If selection is empty, reset
                if (selectedIndex <= 0) {
                    sizeSelect.innerHTML = sizeTemplates.full;
                    return;
                }

                const selectedText = (categorySelect.options[selectedIndex].text || '').toLowerCase();
                let group = 'clothing';

                // Map category names to groups
                if (selectedText.includes('shoe') || selectedText.includes('footwear') || selectedText.includes('sneaker') || selectedText.includes('boot') || selectedText.includes('sandal') || selectedText.includes('heels')) {
                    group = 'shoes';
                } else if (
                    selectedText.includes('accessor') || 
                    selectedText.includes('bag') || 
                    selectedText.includes('hat') || 
                    selectedText.includes('cap') || 
                    selectedText.includes('belt') || 
                    selectedText.includes('scarf') || 
                    selectedText.includes('jewel') || 
                    selectedText.includes('watch') ||
                    selectedText.includes('wallet') ||
                    selectedText.includes('sunglasses')
                ) {
                    group = 'accessories';
                } else if (
                    selectedText.includes('sock') || 
                    selectedText.includes('hosiery') || 
                    selectedText.includes('tights')
                ) {
                    group = 'socks';
                }

                let optionsHtml = sizeTemplates.placeholder;
                
                if (group === 'shoes') {
                    optionsHtml += sizeTemplates.shoes;
                } else if (group === 'accessories') {
                    optionsHtml += sizeTemplates.accessories;
                } else if (group === 'socks') {
                    optionsHtml += sizeTemplates.socks;
                } else {
                    optionsHtml += sizeTemplates.clothing;
                }

                sizeSelect.innerHTML = optionsHtml;
                
                // Preserve old selection if applicable
                const currentSize = "{{ old('size', session('product_step1.size')) }}";
                if (currentSize) {
                    if(optionsHtml.includes(`value="${currentSize}"`)) {
                        sizeSelect.value = currentSize;
                    } else {
                         sizeSelect.value = "";
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                initSizeTemplates();
                updateSizeOptions();
                const categorySelect = document.getElementById('category_id');
                if (categorySelect) categorySelect.addEventListener('change', updateSizeOptions);
            });
        })();
    </script>

    <style>
        .upload-tile {
            border: 2px dashed rgba(209, 213, 219, 1);
            border-radius: 0.5rem;
            background-color: #ffffff;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: block;
        }

        .upload-tile:hover {
            border-color: rgba(156, 163, 175, 1);
        }

        select,
        .space-y-4 select,
        .space-y-8 select {
            position: relative;
            z-index: 10;
        }

        .bg-\[#F4F2ED\] {
            overflow: visible;
        }

        select:focus {
            z-index: 20;
        }

        select#barangay_id {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .space-y-4,
        .space-y-8,
        .bg-\[#F4F2ED\],
        .dark\\:bg-gray-800 {
            overflow: visible !important;
            position: relative !important;
        }
    </style>
</x-app-layout>