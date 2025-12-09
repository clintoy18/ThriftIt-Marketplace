@php
    // Generate S3 URL if a QR code is already in the session
    $existingQr = session('product_qr');
    $existingQrUrl = $existingQr ? Storage::disk('s3')->url($existingQr) : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Optional QR Code Upload') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-step-progress :currentStep="$currentStep" />

            <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-3xl overflow-hidden border-0">
                <div class="bg-gradient-to-r from-[#F8F4EC] via-[#F1E9D2] to-[#E9DFC7] dark:from-gray-800 dark:via-gray-700 dark:to-gray-600 px-8 py-8">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-[#E1D5B6] to-[#D5C39A] rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white/20 dark:ring-gray-800/20">
                            <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">QR Code Setup</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-lg">Make payments easier for buyers</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl p-6 mb-8 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-blue-800 dark:text-blue-300 text-lg mb-1">Why add a QR code?</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-400">Accept instant payments via GCash, Maya, or Bank Transfer.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('sell-item.store-qr') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-lg font-bold text-gray-800 dark:text-gray-200">Upload QR Code Image</label>
                                <span class="text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-3 py-1 rounded-full font-medium">Optional</span>
                            </div>
                            
                            <div class="relative group">
                                <input type="file" name="qr_code" id="qr_code" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(event)">
                                
                                {{-- 1. Apply different classes if file exists --}}
                                <div id="dropZone" 
                                     class="border-3 border-dashed rounded-2xl p-12 text-center transition-all duration-500 
                                     {{ $existingQrUrl ? 'border-green-400 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' : 'border-gray-200 dark:border-gray-600 group-hover:border-[#E1D5B6] group-hover:bg-gradient-to-br group-hover:from-gray-50 group-hover:to-gray-100 dark:group-hover:from-gray-700/50 dark:group-hover:to-gray-800/50' }}">
                                    
                                    {{-- 2. Hide Default Text if file exists --}}
                                    <div id="uploadDefault" class="space-y-6 {{ $existingQrUrl ? 'hidden' : '' }}">
                                        <div class="w-20 h-20 bg-gradient-to-br from-[#F8F4EC] to-[#F1E9D2] dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mx-auto shadow-inner">
                                            <svg class="w-10 h-10 text-gray-400 group-hover:text-[#B59F84] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <div class="space-y-3">
                                            <p class="text-gray-600 dark:text-gray-400 font-semibold text-lg">Drop your QR code here</p>
                                            <p class="text-gray-500 dark:text-gray-500">or <span class="text-[#B59F84] hover:text-[#a08e77] cursor-pointer font-semibold">browse files</span></p>
                                        </div>
                                    </div>

                                    {{-- 3. Show Preview if file exists --}}
                                    <div id="uploadPreview" class="{{ $existingQrUrl ? '' : 'hidden' }} space-y-6">
                                        <div class="relative w-32 h-32 mx-auto">
                                            <div class="w-full h-full border-4 border-white dark:border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                                                <img id="imagePreview" class="w-full h-full object-cover" 
                                                     src="{{ $existingQrUrl ?? '' }}" 
                                                     alt="QR Code Preview">
                                            </div>
                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            <p class="text-gray-700 dark:text-gray-300 font-semibold text-lg" id="fileName">
                                                {{ $existingQrUrl ? 'Current QR Code' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Remove Button --}}
                            <div id="removeButtonContainer" class="{{ $existingQrUrl ? '' : 'hidden' }}">
                                <button type="button" onclick="removeImage()" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl border border-red-200 hover:border-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Remove QR Code
                                </button>
                            </div>
                            @error('qr_code') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-100 dark:border-gray-700">
                            
                            <a href="{{ route('products.create') }}"
                               class="group flex-1 px-6 py-4 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-3 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 text-gray-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Back to Details</span>
                            </a>

                            <button type="submit" formaction="{{ route('sell-item.skip-qr') }}"
                               class="group flex-1 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 border border-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-3 shadow-md">
                                <span>Skip for Now</span>
                            </button>

                            <button type="submit" id="submitBtn"
                                    class="group flex-1 px-6 py-4 bg-gradient-to-r from-[#E1D5B6] via-[#D5C39A] to-[#C9B284] hover:from-[#D5C39A] hover:to-[#BDA776] text-gray-900 font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 relative overflow-hidden">
                                <span class="relative z-10 text-sm font-semibold">Save & Continue</span>
                                <svg class="w-4 h-4 text-gray-800 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('imagePreview');
                    const dropZone = document.getElementById('dropZone');
                    
                    img.src = e.target.result;
                    document.getElementById('uploadDefault').classList.add('hidden');
                    document.getElementById('uploadPreview').classList.remove('hidden');
                    document.getElementById('removeButtonContainer').classList.remove('hidden');
                    document.getElementById('fileName').textContent = input.files[0].name;
                    
                    dropZone.classList.add('border-green-400', 'bg-gradient-to-br', 'from-green-50', 'to-emerald-50');
                    dropZone.classList.remove('border-gray-200');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            document.getElementById('qr_code').value = '';
            document.getElementById('imagePreview').src = '';
            
            document.getElementById('uploadPreview').classList.add('hidden');
            document.getElementById('uploadDefault').classList.remove('hidden');
            document.getElementById('removeButtonContainer').classList.add('hidden');
            
            const dropZone = document.getElementById('dropZone');
            dropZone.classList.remove('border-green-400', 'bg-gradient-to-br', 'from-green-50', 'to-emerald-50');
            dropZone.classList.add('border-gray-200');
        }
    </script>
</x-app-layout>