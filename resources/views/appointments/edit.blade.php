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
                <form method="POST" action="{{ route('appointments.update', $appointment->appointmentid) }}"
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

                    {{-- ================= IMAGE MANAGEMENT SECTION ================= --}}
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Manage Reference Photos
                        </h3>

                        {{-- 1. Existing Images (with Delete option) --}}
                        @if ($appointment->apptImages && $appointment->apptImages->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-semibold">Current Photos
                                    (Select checkboxes to delete on save):</p>
                                <div class="flex gap-3 overflow-x-auto pb-2">
                                    @foreach ($appointment->apptImages as $img)
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ Storage::disk('s3')->url($img->image_path) }}"
                                                alt="Appointment Image"
                                                class="h-28 w-28 object-cover rounded-lg border border-gray-300 dark:border-gray-600">

                                            {{-- Delete Checkbox Overlay --}}
                                            <label
                                                class="absolute top-0 right-0 bg-white/80 dark:bg-gray-800/80 rounded-bl-lg rounded-tr-lg p-1.5 cursor-pointer shadow-sm hover:bg-red-100 dark:hover:bg-red-900/30 transition"
                                                title="Select to delete this image">
                                                <input type="checkbox" name="delete_images[]"
                                                    value="{{ $img->id }}"
                                                    class="rounded border-gray-400 text-red-600 shadow-sm focus:ring-red-500 h-5 w-5">
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('delete_images')" class="mt-2" />
                            </div>
                        @endif

                        {{-- 2. Add New Images (Multiple) --}}
                        <div class="mt-4">
                            <x-input-label for="images" :value="__('Add New Photos (Optional)')" class="mb-2" />
                            <div class="relative group">
                                {{-- Changed name to images[] and added multiple attribute --}}
                                <input id="images" name="images[]" type="file" accept="image/*" multiple
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#B59F84]/10 file:text-[#B59F84] hover:file:bg-[#B59F84]/20">
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                You can select multiple files. These will be added to your existing collection.
                                (Max 5MB per file, JPG/PNG/WEBP).
                            </p>
                            {{-- Updated error handling for array inputs --}}
                            <x-input-error :messages="$errors->get('images')" class="mt-2" />
                            <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                        </div>
                    </div>
                    {{-- ================= END IMAGE SECTION ================= --}}

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('appointments.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B59F84] transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-[#B59F84] hover:bg-[#a08e77] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B59F84] transition shadow-lg">
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
</x-app-layout>
