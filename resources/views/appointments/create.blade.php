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
                class="bg-[#F4F2ED] dark:bg-gray-900 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 p-8 sm:p-10">

                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-8"
                    enctype="multipart/form-data">
                    @csrf

                    @if ($upcycler)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 border-l-4 border-l-[#B59F84] shadow-sm flex items-start justify-between">
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                    Booking With
                                </label>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $upcycler->fname }} {{ $upcycler->lname }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Professional Upcycler</p>
                            </div>
                            <div
                                class="h-12 w-12 rounded-full bg-[#B59F84] flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($upcycler->fname, 0, 1) }}{{ substr($upcycler->lname, 0, 1) }}
                            </div>
                            <input type="hidden" id="upcycler_id" name="upcycler_id" value="{{ $upcycler->id }}">
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#B59F84]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Appointment Details
                            </h3>
                            <div class="h-px flex-1 ml-4 bg-gray-300 dark:bg-gray-700"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="space-y-2">
                                <label for="apptype"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Service
                                    Type</label>
                                <select id="apptype" name="apptype" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm">
                                    <option value="">-- Select Service --</option>
                                    @foreach (['Resize', 'Customize', 'Patchwork', 'Fabric Dyeing'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('apptype') == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('apptype')
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="appdate"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Preferred
                                    Date</label>
                                <input type="date" id="appdate" name="appdate" value="{{ old('appdate') }}"
                                    min="{{ now()->format('Y-m-d') }}" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm">
                                @error('appdate')
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select
                                    Time Slot</label>

                                <div id="time-loading"
                                    class="hidden text-sm text-gray-500 flex items-center gap-2 mb-2">
                                    <svg class="animate-spin h-4 w-4 text-[#B59F84]" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Checking availability...
                                </div>

                                @php
                                    $start = strtotime('08:00');
                                    $end = strtotime('16:30');
                                    $interval = 30 * 60;
                                    $slots = [];
                                    for ($time = $start; $time <= $end; $time += $interval) {
                                        $slots[] = date('H:i', $time);
                                    }
                                @endphp

                                <div id="time-slots-container"
                                    class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                    @foreach ($slots as $slot)
                                        <label class="relative cursor-pointer group time-slot-label"
                                            data-time="{{ $slot }}">
                                            <input type="radio" name="app_time" value="{{ $slot }}"
                                                class="peer sr-only">

                                            <div
                                                class="slot-display px-2 py-3 text-center text-sm font-medium rounded-xl border transition-all duration-200
                                                bg-white text-gray-700 border-gray-300 hover:border-[#B59F84] 
                                                peer-checked:bg-[#B59F84] peer-checked:text-white peer-checked:border-[#B59F84] peer-checked:shadow-md 
                                                dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600
                                                peer-disabled:bg-gray-100 peer-disabled:text-gray-400 peer-disabled:border-gray-200 peer-disabled:cursor-not-allowed peer-disabled:dark:bg-gray-700 peer-disabled:dark:text-gray-500">
                                                {{ date('h:i A', strtotime($slot)) }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('app_time')
                                    <p class="text-sm text-red-600 font-bold flex items-center gap-1 mt-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label for="contactnumber"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Contact
                                    Number</label>
                                <input type="tel" name="contactnumber" pattern="[0-9]{10,11}"
                                    placeholder="09123456789" value="{{ old('contactnumber') }}"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm">
                                @error('contactnumber')
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label for="appdetails"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Instructions & Details
                                </label>
                                <textarea id="appdetails" name="appdetails" rows="5"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#B59F84] focus:border-[#B59F84] transition shadow-sm resize-none"
                                    placeholder="Please describe measurements, fabric type, and specific requests...">{{ old('appdetails') }}</textarea>
                                @error('appdetails')
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#B59F84]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Reference Photos
                            </h3>
                            <div class="h-px flex-1 ml-4 bg-gray-300 dark:bg-gray-700"></div>
                        </div>

                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 mb-5">
                            <div class="flex gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Photo
                                        Guidelines</h4>
                                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                        Upload <strong>Before</strong> photos, <strong>Inspiration</strong> references,
                                        and <strong>Close-ups</strong> of details. Limit 8 photos (Max 5MB each).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="appointmentImages" id="appointmentDropZone"
                                class="upload-tile group cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl transition-all duration-300 hover:border-[#B59F84] hover:bg-white dark:hover:bg-gray-800 bg-white/60 dark:bg-gray-800/50 p-6 min-h-[200px]">

                                <div id="appointmentPreviews"
                                    class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full mb-2 empty:hidden"></div>

                                <div id="appointmentAddMoreText" class="text-center py-2 hidden">
                                    <span
                                        class="text-sm text-[#B59F84] font-bold bg-[#B59F84]/10 px-3 py-1 rounded-full">+
                                        Add more photos</span>
                                </div>

                                <div id="appointmentDropZoneContent"
                                    class="flex flex-col items-center justify-center text-center gap-3 py-6">
                                    <div
                                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:text-[#B59F84] group-hover:scale-110 transition duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#B59F84]">Click to upload</span>
                                        <span class="text-gray-500">or drag and drop</span>
                                    </div>
                                    <p class="text-xs text-gray-400">PNG, JPG, WEBP (Max 8 files)</p>
                                </div>
                            </label>

                            <input id="appointmentImages" name="images[]" type="file" accept="image/*" multiple
                                class="hidden">
                        </div>

                        <div class="mt-2 space-y-1">
                            <p id="appointmentImageError" class="text-sm text-red-600 font-bold hidden"></p>
                            <p id="appointmentReachLimitError" class="text-sm text-red-600 font-bold hidden">Limit
                                reached (Max 8 photos).</p>
                        </div>

                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="w-full bg-[#B59F84] hover:bg-[#a08e77] text-white text-lg font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Confirm Appointment Request
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // 1. DATE RESTRICTIONS
    window.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('appdate');
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        input.min = now.toISOString().slice(0, 16).split('T')[0];
    });

    // 2. CHECK AVAILABILITY LOGIC (AJAX)
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('appdate');
        const upcyclerId = document.getElementById('upcycler_id').value;
        const timeSlots = document.querySelectorAll('input[name="app_time"]');
        const labels = document.querySelectorAll('.time-slot-label');
        const displays = document.querySelectorAll('.slot-display');
        const loader = document.getElementById('time-loading');

        // Function to reset all slots to available state
        function resetSlots() {
            timeSlots.forEach(input => {
                input.disabled = false;
                input.checked = false; // Uncheck previous selection
            });
            displays.forEach(div => {
                // Reset styles to "available"
                div.classList.remove('bg-gray-100', 'text-gray-400', 'border-gray-200',
                    'cursor-not-allowed', 'dark:bg-gray-800', 'dark:border-gray-700',
                    'dark:text-gray-600', 'line-through');
                div.classList.add('bg-white', 'text-gray-700', 'border-gray-300',
                    'hover:border-[#B59F84]', 'dark:bg-gray-800', 'dark:text-gray-200',
                    'dark:border-gray-600');
            });
        }

        // Function to mark a specific slot as booked
        function markAsBooked(timeValue) {
            // Find input with value like "08:30"
            const input = document.querySelector(`input[name="app_time"][value="${timeValue}"]`);
            if (input) {
                input.disabled = true;
                const display = input.nextElementSibling; // The div with class .slot-display

                // Remove available styles
                display.classList.remove('bg-white', 'text-gray-700', 'border-gray-300',
                    'hover:border-[#B59F84]', 'dark:bg-gray-800', 'dark:text-gray-200',
                    'dark:border-gray-600');

                // Add disabled styles
                display.classList.add('bg-gray-100', 'text-gray-400', 'border-gray-200', 'cursor-not-allowed',
                    'dark:bg-gray-800', 'dark:border-gray-700', 'dark:text-gray-600', 'line-through');
            }
        }

        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (!selectedDate || !upcyclerId) return;

            // Show loading
            loader.classList.remove('hidden');
            resetSlots(); // Clear previous state

            // Fetch booked slots
            fetch(
                    `{{ route('appointments.booked-slots') }}?date=${selectedDate}&upcycler_id=${upcyclerId}`)
                .then(response => response.json())
                .then(bookedTimes => {
                    bookedTimes.forEach(time => {
                        // The time comes back as H:i (e.g. 08:30), matches value attribute
                        markAsBooked(time);
                    });
                })
                .catch(error => console.error('Error fetching slots:', error))
                .finally(() => {
                    loader.classList.add('hidden');
                });
        });
    });

    // 3. IMAGE UPLOAD LOGIC (Your existing code)
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('appointmentImages');
        const previews = document.getElementById('appointmentPreviews');
        const dropZone = document.getElementById('appointmentDropZone');
        const addMoreText = document.getElementById('appointmentAddMoreText');
        const dropZoneContent = document.getElementById('appointmentDropZoneContent');
        const errorEl = document.getElementById('appointmentImageError');
        const reachLimitEl = document.getElementById('appointmentReachLimitError');

        let selectedFiles = [];

        function renderPreviews(files) {
            previews.innerHTML = '';
            files.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className =
                    'relative group h-24 w-full rounded-lg overflow-hidden shadow-sm border border-gray-200';

                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';

                const badge = document.createElement('div');
                badge.className =
                    'absolute top-1 left-1 bg-black/60 text-white text-[10px] px-1.5 rounded-sm backdrop-blur-sm';
                badge.textContent = index + 1;

                const removeBtn = document.createElement('button');
                removeBtn.className =
                    'absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs transition shadow-sm';
                removeBtn.innerHTML = '&times;';
                removeBtn.type = 'button';
                removeBtn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    removeAt(index);
                };

                const r = new FileReader();
                r.onload = (ev) => {
                    img.src = ev.target.result;
                };
                r.readAsDataURL(file);

                wrapper.appendChild(img);
                wrapper.appendChild(badge);
                wrapper.appendChild(removeBtn);
                previews.appendChild(wrapper);
            });
            updateUI();
        }

        function updateUI() {
            const count = selectedFiles.length;
            if (count > 0) {
                dropZoneContent.classList.add('hidden');
                addMoreText.classList.remove('hidden');
                dropZone.classList.remove('p-6', 'min-h-[200px]');
                dropZone.classList.add('p-4');
            } else {
                dropZoneContent.classList.remove('hidden');
                addMoreText.classList.add('hidden');
                dropZone.classList.add('p-6', 'min-h-[200px]');
                dropZone.classList.remove('p-4');
            }

            if (count >= 8) {
                if (reachLimitEl) reachLimitEl.classList.remove('hidden');
                addMoreText.classList.add('hidden');
            } else {
                if (reachLimitEl) reachLimitEl.classList.add('hidden');
            }
        }

        function removeAt(index) {
            selectedFiles.splice(index, 1);
            syncInput();
            renderPreviews(selectedFiles);
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }

        input.addEventListener('change', () => {
            const newFiles = Array.from(input.files || []);
            newFiles.forEach(f => {
                if (selectedFiles.length < 8) selectedFiles.push(f);
            });
            syncInput();
            renderPreviews(selectedFiles);
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        dropZone.addEventListener('dragover', () => dropZone.classList.add('border-[#B59F84]', 'bg-gray-50'));
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-[#B59F84]',
            'bg-gray-50'));

        dropZone.addEventListener('drop', (e) => {
            dropZone.classList.remove('border-[#B59F84]', 'bg-gray-50');
            const dt = e.dataTransfer;
            const files = Array.from(dt.files);
            files.forEach(f => {
                if (selectedFiles.length < 8) selectedFiles.push(f);
            });
            syncInput();
            renderPreviews(selectedFiles);
        });
    });
</script>
