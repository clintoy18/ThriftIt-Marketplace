<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100 mt-1">
                {{ __('Request an Appointment') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#F4F2ED] shadow-lg rounded-2xl border border-gray-200 dark:border-gray-600 p-8">
                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    <!-- Display Upcycler Name -->
                    @if ($upcycler)
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Selected Upcycler</label>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $upcycler->fname }} {{ $upcycler->lname }}</p>
                            <input type="hidden" name="upcycler_id" value="{{ $upcycler->id }}">
                        </div>
                    @endif

                    <!-- Appointment Details -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold tracking-wide text-gray-800 dark:text-gray-200">Appointment Details</h3>
                            <div class="h-px flex-1 ml-4 bg-gray-300 dark:bg-gray-700"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Appointment Type -->
                            <div>
                                <label for="apptype" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Appointment Type</label>
                                <select id="apptype" name="apptype" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition" required>
                                    <option value="">-- Select Appointment Type --</option>
                                    @foreach (['Resize', 'Customize', 'Patchwork', 'Fabric Dyeing'] as $type)
                                        <option value="{{ $type }}" {{ old('apptype') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('apptype')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Appointment Date -->
                            <div>
                                <label for="appdate" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Appointment Date & Time</label>
                                <input type="datetime-local" id="appdate" name="appdate"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition"
                                    value="{{ old('appdate') }}"
                                    min="2025-05-02T08:00"
                                    required>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Business hours: 8:00 AM - 6:00 PM</p>
                                @error('appdate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div class="md:col-span-2">
                                <label for="contactnumber" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                                <input type="tel" name="contactnumber" pattern="[0-9]{10,11}" placeholder="09123456789" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl placeholder-gray-500 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition" value="{{ old('contactnumber') }}">
                                @error('contactnumber')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Description -->
                    <div>
                        <label for="appdetails" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Details</label>
                        <textarea id="appdetails" name="appdetails" rows="5" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl placeholder-gray-500 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition resize-none" placeholder="Describe what you'd like to have done...">{{ old('appdetails') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Provide measurements, fabric type, preferred style, or references.</p>
                        @error('appdetails')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Drag & Drop Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Images</label>
                        
                        <!-- Photo Guidelines -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                Photo Guidelines
                            </h4>
                            <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span><strong>Reference Images:</strong> Show what you want done</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span><strong>Before Photos:</strong> Current state of the item</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span><strong>Inspiration:</strong> Examples or style references</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span><strong>Details:</strong> Close-ups of areas needing work</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 mt-0.5">•</span>
                                    <span><strong>Measurements:</strong> Include photos with measurements if applicable</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Upload Drop Zone -->
                        <div class="mb-4">
                            <label for="appointmentImages"
                                   id="appointmentDropZone"
                                   class="upload-tile group cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-300/80 rounded-3xl transition-all duration-500 hover:border-[#B59F84] hover:shadow-xl bg-white/80 hover:bg-white backdrop-blur-sm p-8 min-h-[192px] sm:min-h-[208px] relative">
                                
                                <!-- Preview Grid INSIDE the drop zone -->
                                <div id="appointmentPreviews" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4 w-full"></div>
                                
                                <div id="appointmentAddMoreText" class="text-center mb-4 hidden">
                                    <p class="text-sm text-[#B59F84] font-medium">Tap to add more photos</p>
                                </div>
                                
                                <!-- Drop zone content - only show when no images or can add more -->
                                <div id="appointmentDropZoneContent" class="flex flex-col items-center justify-center gap-5 w-full">
                                    <!-- Icon Container with Gradient -->
                                    <div class="flex justify-center w-full">
                                        <div class="shrink-0 w-18 h-18 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center text-gray-600 transition-all duration-500 group-hover:scale-110 group-hover:from-[#B59F84] group-hover:to-[#a08e77] group-hover:text-white shadow-sm group-hover:shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 5a2 2 0 0 1 2-2h3l2 2h6a2 2 0 0 1 2 2v2H3V5Zm0 6h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Zm9 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Content Container -->
                                    <div class="flex flex-col items-center justify-center gap-4 text-center">
                                        <!-- Browse Files Button -->
                                        <span class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-full bg-gradient-to-r from-[#E1D5B6] to-[#d4c6a2] text-[#6f5e49] transition-all duration-500 group-hover:scale-105 group-hover:shadow-lg group-hover:from-[#d4c6a2] group-hover:to-[#c8b994] transform hover:-translate-y-0.5">
                                            Browse files
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300 group-hover:translate-y-0.5" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 16l-6-6h12l-6 6z"/>
                                            </svg>
                                        </span>

                                        <!-- File Info -->
                                        <div class="flex flex-col gap-2">
                                            <p class="text-sm font-semibold text-gray-700 bg-gray-100/50 px-3 py-1.5 rounded-lg">PNG or JPG up to 5MB each</p>
                                            <span class="text-sm text-gray-600 font-medium">Add or Drag & Drop photos</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hover Glow Effect -->
                                <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-[#B59F84]/20 to-blue-100/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                            </label>
                        </div>
                        
                        <!-- Hidden multiple input -->
                        <input id="appointmentImages" name="images[]" type="file" accept="image/*" multiple class="hidden">

                        <!-- Helper and error -->
                        <p class="mt-2 text-xs text-gray-500">Upload reference images to help the upcycler understand your request.</p>
                        <p id="appointmentImageError" class="mt-2 text-sm text-red-600 hidden"></p>
                        <p id="appointmentReachLimitError" class="mt-2 text-sm text-red-600 hidden">You can only upload up to 8 photos.</p>
                        
                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#B59F84] hover:bg-[#a08e77] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 active:scale-[.98] shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Request Appointment
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Appointment date restrictions
    window.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('appdate');
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const localDatetime = now.toISOString().slice(0, 16);
        input.min = localDatetime;

        input.addEventListener('change', () => {
            const selected = new Date(input.value);
            const hours = selected.getHours();
            if (hours < 8 || hours >= 18) {
                alert("Please select a time between 8:00 AM and 6:00 PM.");
                input.value = '';
            }
        });
    });

    // Multi-image selection with previews, drag & drop, and limit enforcement for appointments
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('appointmentImages');
        const previews = document.getElementById('appointmentPreviews');
        const form = document.querySelector('form');
        const errorEl = document.getElementById('appointmentImageError');
        const dropZone = document.getElementById('appointmentDropZone');
        const addMoreText = document.getElementById('appointmentAddMoreText');
        const dropZoneContent = document.getElementById('appointmentDropZoneContent');
        const reachLimitEl = document.getElementById('appointmentReachLimitError');
        let selectedFiles = [];

        function showError(msg) {
            if (!errorEl) return;
            errorEl.textContent = msg;
            errorEl.classList.remove('hidden');
        }
        function hideError() {
            if (!errorEl) return;
            errorEl.classList.add('hidden');
        }

        function showReachLimit() {
            if (!reachLimitEl) return;
            reachLimitEl.classList.remove('hidden');
            setTimeout(() => reachLimitEl.classList.add('hidden'), 2500);
        }
        function hideReachLimit() {
            if (!reachLimitEl) return;
            reachLimitEl.classList.add('hidden');
        }

        function renderPreviews(files) {
            previews.innerHTML = '';
            files.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-item relative';
                const img = document.createElement('img');
                img.alt = 'Preview ' + (index + 1);
                img.className = 'w-full h-24 object-cover rounded-lg';
                const badge = document.createElement('span');
                badge.className = 'preview-number absolute top-1 left-1 bg-black bg-opacity-70 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                badge.textContent = index + 1;
                const removeBtn = document.createElement('span');
                removeBtn.className = 'remove-btn absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs cursor-pointer';
                removeBtn.textContent = '×';
                removeBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); removeAt(index); };
                wrapper.appendChild(img);
                wrapper.appendChild(badge);
                wrapper.appendChild(removeBtn);
                previews.appendChild(wrapper);
                const r = new FileReader();
                r.onload = (ev) => { img.src = ev.target.result; };
                r.readAsDataURL(file);
            });
            
            // Update drop zone content visibility and auto-height
            updateDropZoneVisibility();
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
            return selectedFiles.length < 8;
        }

        function updateDropZoneVisibility() {
            const total = selectedFiles.length;
            
            // Show/hide drop zone content based on whether we have images
            if (total > 0) {
                dropZoneContent.classList.add('hidden');
                // Auto adjust height based on content
                if (canAddMore()) {
                    addMoreText.classList.remove('hidden');
                } else {
                    addMoreText.classList.add('hidden');
                }
                dropZone.style.minHeight = 'auto';
            } else {
                dropZoneContent.classList.remove('hidden');
                addMoreText.classList.add('hidden');
                dropZone.style.minHeight = '192px';
            }
            
            // Show reach limit message and disable adding more
            if (total >= 8) {
                showReachLimit();
                addMoreText.classList.add('hidden');
            } else {
                hideReachLimit();
            }
        }

        // Clear input before opening via label (ensures first pick registers)
        if (dropZone) {
            dropZone.addEventListener('mousedown', () => { if (input) input.value = ''; });
        }

        input.addEventListener('change', () => {
            hideError();
            const newly = Array.from(input.files || []);
            const makeKey = (f) => `${f.name}|${f.size}|${f.lastModified}`;
            const keys = new Set(selectedFiles.map(makeKey));
            for (const f of newly) {
                if (selectedFiles.length >= 8) break;
                const k = makeKey(f);
                if (keys.has(k)) continue;
                selectedFiles.push(f);
                keys.add(k);
            }
            if (selectedFiles.length >= 8 && newly.length > 0) {
                showReachLimit();
            }
            syncInput();
            renderPreviews(selectedFiles);
        });

        form.addEventListener('submit', (e) => {
            const count = input.files?.length || selectedFiles.length;
            if (count > 8) {
                e.preventDefault();
                showError('Please upload up to 8 photos only.');
            }
        });

        // Drag & Drop support
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (canAddMore()) {
                    dropZone.classList.add('ring-2', 'ring-[#B59F84]');
                } else {
                    dropZone.classList.add('ring-2', 'ring-red-400');
                }
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
                    if (selectedFiles.length >= 8) break;
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
            dropZone.addEventListener('click', (e) => {
                if (!canAddMore()) {
                    e.preventDefault();
                    showReachLimit();
                }
            });
        }

        // Initialize
        renderPreviews(selectedFiles);
    });
</script>

<style>
    /* Upload tile styling */
    .upload-tile {
        border: 2px dashed rgba(209, 213, 219, 0.8);
        border-radius: 1.5rem;
        background-color: rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        display: block;
    }
    .upload-tile:hover { 
        border-color: #B59F84; 
    }
    
    /* Preview grid styling */
    #appointmentPreviews .preview-item {
        position: relative;
        height: 100px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    #appointmentPreviews .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #appointmentPreviews .preview-number {
        position: absolute;
        top: 5px;
        left: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-center;
        font-size: 12px;
        font-weight: 600;
    }
    #appointmentPreviews .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-center;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
    }
    #appointmentPreviews .remove-btn:hover {
        background: rgba(220, 38, 38, 1);
        transform: scale(1.1);
    }
</style>
