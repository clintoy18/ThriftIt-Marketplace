@php
    // Helper to fetch names from IDs stored in session
    // We use optional() or null coalescing to prevent crashes if session data is missing
    $category = isset($step1['category_id']) ? \App\Models\Categories::find($step1['category_id']) : null;
    $segment  = isset($step1['segment_id'])  ? \App\Models\Segment::find($step1['segment_id']) : null;
    $barangay = isset($step1['barangay_id']) ? \App\Models\Barangay::find($step1['barangay_id']) : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finalize Item') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if (auth()->user()->is_verified)
                <x-step-progress :currentStep="$currentStep" />
            @else
                {{-- Unverified Banner --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-200">Ready to Publish</h4>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5">You are publishing as a Standard Seller (No QR).</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-3xl overflow-hidden border-0">
                <div class="bg-gradient-to-r from-[#F8F4EC] via-[#F1E9D2] to-[#E9DFC7] dark:from-gray-800 dark:via-gray-700 dark:to-gray-600 px-8 py-8">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-[#E1D5B6] to-[#D5C39A] rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white/20 dark:ring-gray-800/20">
                            <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Final Review</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-lg">Review your item details before publishing</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    
                    {{-- PRODUCT PHOTOS SECTION --}}
                    <div class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 shadow-sm border border-[#E9DFC7] dark:border-gray-600 mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Item Photos
                        </h4>
                        
                        @if(!empty($images))
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach($images as $image)
                                    <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm group">
                                        {{-- Using S3 temporaryUrl ensures visibility even if bucket policies are strict --}}
                                        <img src="{{ Storage::disk('s3')->temporaryUrl($image, now()->addMinutes(60)) }}" 
                                             alt="Item preview" 
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-red-500 italic">No images found. Please go back and upload photos.</p>
                        @endif
                    </div>

                    {{-- PRODUCT INFO SECTION --}}
                    <div class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 shadow-sm border border-[#E9DFC7] dark:border-gray-600 mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Item Information
                        </h4>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-[#F1E9D2] dark:border-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Name</span>
                                    <span class="text-gray-800 dark:text-white font-semibold">{{ $step1['name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-[#F1E9D2] dark:border-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Price</span>
                                    <span class="text-green-600 dark:text-green-400 font-bold">₱{{ number_format($step1['price'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Category</span>
                                    <span class="text-gray-800 dark:text-white font-semibold">{{ $category->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-[#F1E9D2] dark:border-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Segment</span>
                                    <span class="text-gray-800 dark:text-white font-semibold">{{ $segment->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Barangay</span>
                                    <span class="text-gray-800 dark:text-white font-semibold">{{ $barangay->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QR CODE SECTION --}}
                    <div class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 shadow-sm border border-[#E9DFC7] dark:border-gray-600 mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            QR Code
                        </h4>

                        @if ($qr && !empty(trim($qr)))
                            <div class="flex flex-col items-center text-center">
                                <div class="w-40 h-40 bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-2 border-2 border-[#E1D5B6] dark:border-gray-600 mb-4 flex items-center justify-center overflow-hidden">
                                    {{-- FIX: Using temporaryUrl to handle S3 preview --}}
                                    <img src="{{ Storage::disk('s3')->temporaryUrl($qr, now()->addMinutes(60)) }}" 
                                         alt="QR Code" 
                                         class="w-full h-full object-contain rounded-lg">
                                </div>
                                <p class="text-green-600 dark:text-green-400 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    QR Code uploaded successfully
                                </p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="w-16 h-16 bg-[#F8F4EC] dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 font-medium mb-2">No QR Code Uploaded</p>
                                @if (!auth()->user()->is_verified)
                                    <p class="text-sm text-amber-600 dark:text-amber-400 font-medium">
                                        QR code upload is not available for unverified accounts.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('sell-item.finalize') }}" method="POST">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-100 dark:border-gray-700">
                            
                            {{-- BACK BUTTON LOGIC --}}
                            @if (auth()->user()->is_verified)
                                <a href="{{ route('sell-item.qr-step') }}"
                                   class="group flex-1 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 dark:from-gray-700 dark:to-gray-600 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-200 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-3 shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:scale-[1.02]">
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center group-hover:bg-gray-300 dark:group-hover:bg-gray-500 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                    </div>
                                    <div class="text-left flex-1">
                                        <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Back</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Previous step</div>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('products.create') }}"
                                   class="group flex-1 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 dark:from-gray-700 dark:to-gray-600 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-200 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-3 shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:scale-[1.02]">
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center group-hover:bg-gray-300 dark:group-hover:bg-gray-500 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                    </div>
                                    <div class="text-left flex-1">
                                        <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Edit Details</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Go back to form</div>
                                    </div>
                                </a>
                            @endif

                            <button type="submit"
                                class="group flex-1 px-6 py-4 bg-gradient-to-r from-[#E1D5B6] via-[#D5C39A] to-[#C9B284] hover:from-[#D5C39A] hover:via-[#C9B284] hover:to-[#BDA776] text-gray-900 font-semibold rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-3 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm ring-2 ring-white/30 z-10">
                                    <svg class="w-5 h-5 text-gray-800 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="text-left flex-1 z-10">
                                    <div class="text-sm font-semibold text-gray-700">Publish Item</div>
                                    <div class="text-xs text-gray-700/80">Go live now</div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 text-center">
                  <div class="inline-flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-2xl px-5 py-3 shadow-md border border-gray-200 dark:border-gray-700">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Need help? Contact our support anytime.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>