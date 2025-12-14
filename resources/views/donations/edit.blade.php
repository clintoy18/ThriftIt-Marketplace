<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Donation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. REJECTION NOTICE BANNER (Kept this for visibility) --}}
            @if ($donation->approval_status === 'rejected' || $donation->approval_status === 'changes_requested')
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800 dark:text-red-200">Action Required: Donation
                                Rejected/Changes Needed</h3>

                            @if ($donation->admin_notes)
                                <div class="mt-1 text-sm text-red-700 dark:text-red-300">
                                    <strong>Admin Notes:</strong> {{ $donation->admin_notes }}
                                </div>
                            @endif

                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                                Please correct the details below based on the notes. Clicking "Update & Resubmit" will
                                automatically
                                <strong>send this item back to Pending</strong> for admin review.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="bg-[#F4F2ED] dark:bg-gray-800/90 backdrop-blur overflow-hidden shadow-xl sm:rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                <form id="donationEditForm" method="POST" action="{{ route('donations.update', $donation) }}"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name --}}
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="name" :value="__('Donation Name')" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-2 block w-full rounded-xl" :value="old('name', $donation->name)" required autofocus />
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select name="category_id" id="category_id"
                                class="block w-full mt-2 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#E1D5B6] focus:outline-none">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $donation->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <x-input-label for="status" :value="__('Status')" />

                            {{-- CASE 1: IF REJECTED - LOCK STATUS --}}
                            @if ($donation->approval_status === 'rejected' || $donation->approval_status === 'pending')
                                <div
                                    class="mt-2 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed flex items-center justify-between">
                                    <span>Pending Approval (Resubmission)</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                {{-- Force value to pending so backend knows --}}
                                <input type="hidden" name="status" value="pending">

                                {{-- CASE 2: NORMAL - ALLOW EDIT --}}
                            @else
                                <select id="status" name="status"
                                    class="w-full mt-2 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#E1D5B6] focus:outline-none"
                                    required>
                                    <option value="available" @if ($donation->status === 'donated') disabled @endif
                                        {{ old('status', $donation->status) === 'available' ? 'selected' : '' }}>
                                        Available
                                    </option>
                                    <option value="donated"
                                        {{ old('status', $donation->status) === 'donated' ? 'selected' : '' }}>
                                        Donated
                                    </option>
                                    {{-- Preserve pending if it's currently pending but NOT rejected (e.g. first time upload) --}}
                                    @if ($donation->status === 'pending')
                                        <option value="pending" selected>Pending Approval</option>
                                    @endif
                                </select>
                            @endif

                            @if ($donation->status === 'donated')
                                <p class="text-sm text-red-600 mt-1">This donation is marked as donated.</p>
                            @endif
                            @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5"
                                class="mt-2 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#E1D5B6] focus:outline-none resize-none"
                                required>{{ old('description', $donation->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Image Upload Section (Advanced S3 Logic) --}}
                    <div>
                        <x-input-label for="images" :value="__('Donation Images')" />

                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
                            <h4
                                class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                </svg>
                                Photo Guidelines
                            </h4>
                            <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                                <li class="flex items-start gap-2"><span
                                        class="text-blue-500 mt-0.5">•</span><span><strong>Lighting:</strong> Ensure
                                        item is well-lit.</span></li>
                                <li class="flex items-start gap-2"><span
                                        class="text-blue-500 mt-0.5">•</span><span><strong>Angles:</strong> Show front,
                                        back, and defects.</span></li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label for="donationImages" id="donationDropZone"
                                class="upload-tile group cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-300/80 rounded-3xl transition-all duration-500 hover:border-primary-400 hover:shadow-xl bg-white/80 hover:bg-white backdrop-blur-sm p-8 min-h-[192px] sm:min-h-[208px]">

                                {{-- New Previews --}}
                                <div id="donationPreviews" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4 w-full">
                                </div>

                                {{-- Existing Images --}}
                                <div id="existingImagesContainer" class="flex flex-wrap gap-3 mb-4 w-full">
                                    @foreach ($donation->donationImages as $img)
                                        <div class="relative group existing-img-item" data-id="{{ $img->id }}">
                                            {{-- Using temporaryUrl for S3 security/compatibility --}}
                                            <img src="{{ Storage::disk('s3')->temporaryUrl($img->image, now()->addMinutes(60)) }}"
                                                alt="Donation Image" class="w-24 h-24 object-cover rounded-xl border">

                                            <button type="button" data-id="{{ $img->id }}"
                                                class="absolute top-0 right-0 -translate-x-1/4 -translate-y-1/4 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-70 hover:opacity-100 delete-image-btn">
                                                &times;
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- "Add More" Text --}}
                                <div id="donationAddMoreText" class="text-center mb-4 hidden">
                                    <p class="text-sm text-[#B59F84] font-medium">Tap to add more photos</p>
                                </div>

                                {{-- Drop Zone Content --}}
                                <div id="dropZoneContent"
                                    class="flex flex-col items-center justify-center gap-5 w-full">
                                    <div class="flex justify-center w-full">
                                        <div
                                            class="shrink-0 w-18 h-18 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center text-gray-600 transition-all duration-500 group-hover:scale-110 shadow-sm group-hover:shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 5a2 2 0 0 1 2-2h3l2 2h6a2 2 0 0 1 2 2v2H3V5Zm0 6h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Zm9 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center justify-center gap-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-full bg-gradient-to-r from-[#E1D5B6] to-[#d4c6a2] text-[#6f5e49] transition-all duration-500 group-hover:scale-105 shadow-lg group-hover:from-[#d4c6a2] group-hover:to-[#c8b994]">
                                            Browse files
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 16l-6-6h12l-6 6z" />
                                            </svg>
                                        </span>
                                        <div class="flex flex-col gap-2">
                                            <p
                                                class="text-sm font-semibold text-gray-700 bg-gray-100/50 px-3 py-1.5 rounded-lg">
                                                PNG or JPG up to 5MB each</p>
                                            <span class="text-sm text-gray-600 font-medium">Add or Drag & Drop
                                                photos</span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute inset-0 rounded-3xl bg-gradient-to-r from-primary-100/20 to-blue-100/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10">
                                </div>
                            </label>
                        </div>

                        <input id="donationImages" name="images[]" type="file" accept="image/*" multiple
                            class="hidden">

                        {{-- Counters and Error Messages --}}
                        <p class="mt-2 text-xs text-gray-500">Upload 2–8 photos. You currently have <span
                                id="currentImageCount">{{ count($donation->donationImages) }}</span> images.</p>
                        <p id="donationImageError" class="mt-2 text-sm text-red-600 hidden"></p>
                        <p id="donationReachLimitError" class="mt-2 text-sm text-red-600 hidden">You can only upload
                            up to 8 photos.</p>
                    </div>

                    <div
                        class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white bg-[#B59F84] hover:bg-[#a08e77] shadow-lg transition-transform transform hover:scale-[1.02]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            @if ($donation->approval_status === 'rejected' || $donation->approval_status === 'changes_requested')
                                Update & Resubmit
                            @else
                                Update Donation
                            @endif
                        </button>
                        <a href="{{ route('donations.index') }}"
                            class="text-gray-600 hover:text-gray-800 underline-offset-2 hover:underline">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Javascript Logic (Identical to Products but mapped for Donations) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('donationImages');
            const previews = document.getElementById('donationPreviews');
            const form = document.getElementById('donationEditForm');
            const errorEl = document.getElementById('donationImageError');
            const dropZone = document.getElementById('donationDropZone');
            const addMoreText = document.getElementById('donationAddMoreText');
            const dropZoneContent = document.getElementById('dropZoneContent');
            const reachLimitEl = document.getElementById('donationReachLimitError');
            const existingImagesContainer = document.getElementById('existingImagesContainer');
            const currentImageCountEl = document.getElementById('currentImageCount');
            let selectedFiles = [];

            function showError(msg) {
                if (errorEl) {
                    errorEl.textContent = msg;
                    errorEl.classList.remove('hidden');
                }
            }

            function hideError() {
                if (errorEl) errorEl.classList.add('hidden');
            }

            function showReachLimit() {
                if (reachLimitEl) {
                    reachLimitEl.classList.remove('hidden');
                    setTimeout(() => reachLimitEl.classList.add('hidden'), 2500);
                }
            }

            function hideReachLimit() {
                if (reachLimitEl) reachLimitEl.classList.add('hidden');
            }

            function getExistingCount() {
                if (!existingImagesContainer) return 0;
                return existingImagesContainer.querySelectorAll('.existing-img-item:not([data-deleted="true"])')
                    .length;
            }

            function getNewCount() {
                return selectedFiles.length;
            }

            function getTotalCount() {
                return getExistingCount() + getNewCount();
            }

            function updateImageCount() {
                if (currentImageCountEl) currentImageCountEl.textContent = getTotalCount();
            }

            function renderPreviews(files) {
                previews.innerHTML = '';
                files.forEach((file, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-item relative';
                    const img = document.createElement('img');
                    img.className = 'w-full h-24 object-cover rounded-lg';
                    const badge = document.createElement('span');
                    badge.className =
                        'preview-number absolute top-1 left-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                    badge.textContent = index + 1;
                    const removeBtn = document.createElement('span');
                    removeBtn.className =
                        'remove-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs cursor-pointer';
                    removeBtn.textContent = '×';
                    removeBtn.onclick = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        removeAt(index);
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(badge);
                    wrapper.appendChild(removeBtn);
                    previews.appendChild(wrapper);

                    const r = new FileReader();
                    r.onload = (ev) => {
                        img.src = ev.target.result;
                    };
                    r.readAsDataURL(file);
                });
                updateDropZoneVisibility();
                updateImageCount();
            }

            function syncInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                input.files = dt.files;
            }

            function removeAt(idx) {
                selectedFiles.splice(idx, 1);
                syncInput();
                renderPreviews(selectedFiles);
                hideError();
            }

            function canAddMore() {
                return getTotalCount() < 8;
            }

            function updateDropZoneVisibility() {
                const total = getTotalCount();
                if (total > 0) {
                    dropZoneContent.classList.add('hidden');
                    if (canAddMore()) addMoreText.classList.remove('hidden');
                    else addMoreText.classList.add('hidden');
                    dropZone.style.minHeight = 'auto';
                } else {
                    dropZoneContent.classList.remove('hidden');
                    addMoreText.classList.add('hidden');
                    dropZone.style.minHeight = '192px';
                }

                if (!canAddMore()) {
                    showReachLimit();
                    addMoreText.classList.add('hidden');
                } else {
                    hideReachLimit();
                }
            }

            const label = document.querySelector('label[for="donationImages"]');
            if (label) label.addEventListener('mousedown', () => {
                if (input) input.value = '';
            });

            input.addEventListener('change', () => {
                hideError();
                const newly = Array.from(input.files || []);
                const makeKey = (f) => `${f.name}|${f.size}|${f.lastModified}`;
                const keys = new Set(selectedFiles.map(makeKey));
                for (const f of newly) {
                    if (getTotalCount() >= 8) break;
                    const k = makeKey(f);
                    if (keys.has(k)) continue;
                    selectedFiles.push(f);
                    keys.add(k);
                }
                if (getTotalCount() >= 8 && newly.length > 0) showReachLimit();
                syncInput();
                renderPreviews(selectedFiles);
            });

            // Drag & Drop
            if (dropZone) {
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (canAddMore()) dropZone.classList.add('ring-2', 'ring-blue-400');
                    else dropZone.classList.add('ring-2', 'ring-red-400');
                });
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('ring-2', 'ring-blue-400', 'ring-red-400');
                });
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('ring-2', 'ring-blue-400', 'ring-red-400');
                    if (!canAddMore()) {
                        showReachLimit();
                        return;
                    }
                    const files = Array.from(e.dataTransfer.files || []);
                    const makeKey = (f) => `${f.name}|${f.size}|${f.lastModified}`;
                    const keys = new Set(selectedFiles.map(makeKey));
                    let added = false;
                    for (const f of files) {
                        if (getTotalCount() >= 8) break;
                        const k = makeKey(f);
                        if (keys.has(k)) continue;
                        selectedFiles.push(f);
                        keys.add(k);
                        added = true;
                    }
                    if (added) {
                        syncInput();
                        renderPreviews(selectedFiles);
                    }
                });
            }

            // Delete existing images logic
            document.querySelectorAll('.delete-image-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const imageId = this.getAttribute('data-id');
                    const wrapper = this.closest('.existing-img-item');

                    wrapper.style.display = 'none';
                    wrapper.setAttribute('data-deleted', 'true');

                    // Create hidden input: name="deletedImages[]" to match controller
                    if (form && !form.querySelector(
                            `input[name="deletedImages[]"][value="${imageId}"]`)) {
                        const deletedInput = document.createElement('input');
                        deletedInput.type = 'hidden';
                        deletedInput.name = 'deletedImages[]'; // Matches your Controller
                        deletedInput.value = imageId;
                        form.appendChild(deletedInput);
                    }

                    updateDropZoneVisibility();
                    updateImageCount();
                });
            });

            form.addEventListener('submit', (e) => {
                const count = getTotalCount();
                if (count < 2) {
                    e.preventDefault();
                    showError('Please keep at least 2 photos.');
                }
            });

            updateDropZoneVisibility();
        });
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

        #donationPreviews .preview-item {
            position: relative;
            height: 100px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #donationPreviews .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</x-app-layout>
