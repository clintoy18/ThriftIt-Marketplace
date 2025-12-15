<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
                {{ __('Edit Appointment') }}
            </h2>

            {{-- BACK BUTTON --}}
            <a href="{{ route('appointments.index') }}"
                class="inline-flex items-center px-4 py-2 bg-[#B59F84] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#6B5B48] active:bg-[#6B5B48] focus:outline-none focus:border-[#6B5B48] focus:ring ring-[#B59F84] disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-[#F4F2ED] dark:bg-gray-900 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700 p-8">
                <form id="apptEditForm" method="POST"
                    action="{{ route('appointments.update', $appointment->appointmentid) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Read-only fields (Upcycler & Time) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Scheduled with
                            Upcycler</label>
                        <x-text-input type="text"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed"
                            :value="$appointment->upcycler->fname . ' ' . $appointment->upcycler->lname" disabled />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Appointment
                            Time</label>
                        <x-text-input type="text"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed"
                            :value="\Carbon\Carbon::parse($appointment->appdate)
                                ->setTimeFromTimeString($appointment->app_time)
                                ->format('M d, Y h:i A')" disabled />
                    </div>

                    {{-- Status Dropdown (Disabled for user) --}}
                    <div class="mb-4">
                        <x-input-label for="appstatus" :value="__('Appointment Status')" />
                        <select id="appstatus_display"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 focus:ring-0 focus:border-gray-300 cursor-not-allowed transition shadow-sm"
                            disabled>
                            @foreach (['pending', 'approved', 'completed', 'declined', 'rejected', 'cancelled'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('appstatus', $appointment->appstatus) === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="appstatus" value="{{ $appointment->appstatus }}">
                        <x-input-error :messages="$errors->get('appstatus')" class="mt-2" />
                    </div>

                    {{-- Details & Contact --}}
                    <div class="mb-4">
                        <x-input-label for="appdetails" :value="__('Appointment Details')" />
                        <textarea id="appdetails" name="appdetails" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm resize-none"
                            required>{{ old('appdetails', $appointment->appdetails) }}</textarea>
                        <x-input-error :messages="$errors->get('appdetails')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="contactnumber" :value="__('Contact Number')" />
                        <x-text-input id="contactnumber" name="contactnumber" type="text"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition"
                            :value="old('contactnumber', $appointment->contactnumber)" required />
                        <x-input-error :messages="$errors->get('contactnumber')" class="mt-2" />
                    </div>

                    {{-- ================= DRAG & DROP IMAGE SECTION ================= --}}
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <x-input-label for="images" :value="__('Reference Photos')" class="mb-2" />

                        {{-- Instructions --}}
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
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                Please upload clear reference photos for your appointment.
                                You can upload up to 8 images (JPG/PNG/WEBP, max 5MB each).
                            </p>
                        </div>

                        {{-- Drop Zone --}}
                        <div class="mb-4">
                            <label for="apptImagesInput" id="apptDropZone"
                                class="upload-tile group cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-300/80 rounded-3xl transition-all duration-500 hover:border-[#B59F84] hover:shadow-xl bg-white/80 hover:bg-white backdrop-blur-sm p-8 min-h-[192px] sm:min-h-[208px]">

                                {{-- Previews Container (New & Existing) --}}
                                <div id="apptPreviews" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4 w-full"></div>

                                <div id="existingImagesContainer" class="flex flex-wrap gap-3 mb-4 w-full">
                                    @if ($appointment->apptImages)
                                        @foreach ($appointment->apptImages as $img)
                                            <div class="relative group existing-img-item">
                                                {{-- Using url() based on your original S3 config --}}
                                                <img src="{{ Storage::disk('s3')->url($img->image_path) }}"
                                                    alt="Appointment Image"
                                                    class="w-24 h-24 object-cover rounded-xl border border-gray-200">

                                                {{-- Delete Button --}}
                                                <button type="button" data-id="{{ $img->id }}"
                                                    class="absolute top-0 right-0 -translate-x-1/4 -translate-y-1/4 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-70 hover:opacity-100 delete-image-btn shadow-md transition-all">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- "Add More" Text (visible when files exist) --}}
                                <div id="addMoreText" class="text-center mb-4 hidden">
                                    <p class="text-sm text-[#B59F84] font-medium">Tap to add more photos</p>
                                </div>

                                {{-- Default Empty State --}}
                                <div id="dropZoneContent"
                                    class="flex flex-col items-center justify-center gap-5 w-full">
                                    <div class="flex justify-center w-full">
                                        <div
                                            class="shrink-0 w-18 h-18 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center text-gray-600 transition-all duration-500 group-hover:scale-110 shadow-sm group-hover:shadow-md p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3 5a2 2 0 0 1 2-2h3l2 2h6a2 2 0 0 1 2 2v2H3V5Zm0 6h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Zm9 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center justify-center gap-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-full bg-gradient-to-r from-[#B59F84] to-[#a08e77] text-white transition-all duration-500 group-hover:scale-105 shadow-lg">
                                            Browse files
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 16l-6-6h12l-6 6z" />
                                            </svg>
                                        </span>
                                        <div class="flex flex-col gap-2">
                                            <p
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100/50 dark:bg-gray-800/50 px-3 py-1.5 rounded-lg">
                                                PNG, JPG or WEBP up to 5MB</p>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Add or
                                                Drag & Drop photos</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- Hidden File Input --}}
                        <input id="apptImagesInput" name="images[]" type="file" accept="image/*" multiple
                            class="hidden">

                        {{-- Counts & Errors --}}
                        <p class="mt-2 text-xs text-gray-500">
                            Upload 1–8 photos. You currently have <span
                                id="currentImageCount">{{ $appointment->apptImages->count() }}</span> images.
                        </p>
                        <p id="imageError" class="mt-2 text-sm text-red-600 hidden"></p>
                        <p id="reachLimitError" class="mt-2 text-sm text-red-600 hidden">You can only upload up to 8
                            photos.</p>
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                        <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('appointments.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B59F84] transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-[#B59F84] hover:bg-[#a08e77] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B59F84] transition shadow-lg transform hover:scale-[1.02]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Update Appointment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('apptImagesInput');
            const previews = document.getElementById('apptPreviews');
            const form = document.getElementById('apptEditForm');
            const errorEl = document.getElementById('imageError');
            const dropZone = document.getElementById('apptDropZone');
            const addMoreText = document.getElementById('addMoreText');
            const dropZoneContent = document.getElementById('dropZoneContent');
            const reachLimitEl = document.getElementById('reachLimitError');
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
                // Count items that are NOT marked for deletion
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
                    img.className = 'w-full h-24 object-cover rounded-lg border border-gray-200';

                    const badge = document.createElement('span');
                    badge.className =
                        'preview-number absolute top-1 left-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                    badge.textContent = index + 1;

                    const removeBtn = document.createElement('span');
                    removeBtn.className =
                        'remove-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs cursor-pointer shadow-md hover:bg-red-600 transition';
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

            // Prevent input click loop when clicking label
            const label = document.querySelector('label[for="apptImagesInput"]');
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
                    if (canAddMore()) dropZone.classList.add('ring-2', 'ring-[#B59F84]');
                    else dropZone.classList.add('ring-2', 'ring-red-400');
                });
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('ring-2', 'ring-[#B59F84]', 'ring-red-400');
                });
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('ring-2', 'ring-[#B59F84]', 'ring-red-400');
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
                        // Basic image validation
                        if (!f.type.startsWith('image/')) continue;

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

                    // Mark as deleted visually
                    wrapper.style.display = 'none';
                    wrapper.setAttribute('data-deleted', 'true');

                    // Create hidden input to tell controller to delete
                    // Using 'delete_images[]' to match your original controller expectation
                    if (form && !form.querySelector(
                            `input[name="delete_images[]"][value="${imageId}"]`)) {
                        const deletedInput = document.createElement('input');
                        deletedInput.type = 'hidden';
                        deletedInput.name = 'delete_images[]';
                        deletedInput.value = imageId;
                        form.appendChild(deletedInput);
                    }

                    updateDropZoneVisibility();
                    updateImageCount();
                });
            });

            // Initial render
            updateDropZoneVisibility();
        });
    </script>

    {{-- STYLES --}}
    <style>
        .upload-tile {
            border: 2px dashed rgba(209, 213, 219, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: block;
        }

        .upload-tile:hover {
            border-color: #B59F84;
        }

        #apptPreviews .preview-item {
            position: relative;
            height: 100px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #apptPreviews .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</x-app-layout>