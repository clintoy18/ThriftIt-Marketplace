<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $donation->name }}
        </h2>
    </x-slot> --}}
    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto p-6">

            {{-- 1. REJECTION/STATUS BANNER --}}
            {{-- CHANGED: status -> approval_status --}}
            @if ($donation->approval_status === 'rejected' || $donation->approval_status === 'changes_requested')
                <div class="rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                This donation listing needs attention
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <p>
                                    This listing does not meet our current guidelines.
                                    @if (!empty($donation->admin_notes))
                                        <br><span class="font-bold mt-1 block">Admin Notes:
                                            {{ $donation->admin_notes }}</span>
                                    @endif
                                </p>
                                @if (Auth::id() === $donation->user_id)
                                    <p class="mt-2 font-medium">
                                        Action Required: Please update the donation details to resolve the issue and
                                        resubmit for approval.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8 items-stretch">
                <div class="lg:w-1/3 flex flex-col gap-6 h-full">
                    <div class="relative swiper mySwiper rounded-xl overflow-hidden shadow-lg h-[28rem] sm:h-[32rem]">
                        <div class="swiper-wrapper h-full">
                            @if ($donation->donationImages && $donation->donationImages->count() > 0)
                                @foreach ($donation->donationImages as $image)
                                    <div class="swiper-slide flex items-center justify-center bg-white h-full">
                                        <img src="{{ Storage::disk('s3')->url($image->image) }}"
                                            alt="{{ $image->name }}"
                                            class="w-full h-full object-cover transition-transform duration-500 ease-out hover:scale-105">
                                    </div>
                                @endforeach
                            @else
                                <div class="swiper-slide flex items-center justify-center bg-white h-full">
                                    <img src="{{ asset('images/default-placeholder.png') }}" alt="No image"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div
                                class="absolute top-2 left-2 z-10 dark:bg-green-300 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full shadow">
                                Free
                            </div>
                        </div>

                        <div class="swiper-pagination absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10"></div>
                        <div
                            class="swiper-button-next !text-white text-3xl z-20 hover:!text-gray-200 transition-colors duration-300">
                        </div>
                        <div
                            class="swiper-button-prev !text-white text-3xl z-20 hover:!text-gray-200 transition-colors duration-300">
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $donation->name }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Size: {{ $donation->size }} ·
                            {{-- {{ ucfirst($donation->condition) }} --}}
                            {{ $donation->category->name ?? 'No Category' }}
                        </p>

                        {{-- 2. STATUS BADGES --}}
                        <div class="flex items-center gap-2 mt-4 mb-4">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Status:</span>

                            {{-- CHANGED: status -> approval_status --}}
                            @if ($donation->approval_status === 'rejected' || $donation->approval_status === 'changes_requested')
                                <span
                                    class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/30 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/10">
                                    Rejected / Changes Requested
                                </span>

                                {{-- CHANGED: status -> approval_status --}}
                            @elseif($donation->approval_status === 'pending')
                                <span
                                    class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300 ring-1 ring-inset ring-yellow-600/20">
                                    Pending Approval
                                </span>
                            @elseif($donation->status === 'donated')
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10">
                                    Donated
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20">
                                    Available
                                </span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Description</h2>
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                <p class="text-gray-800 dark:text-gray-200 break-words overflow-hidden">
                                    {{ $donation->description ?? 'No description available' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-3">
                            <p class="text-lg font-bold text-[#B59F84]">Free Donation</p>

                            @if (Auth::id() === $donation->user_id)
                                <div class="flex flex-col gap-3 mt-4">
                                @if($donation->status !== 'donated')
                                    <a href="{{ route('donations.edit', $donation->id) }}"
                                        class="px-6 py-3 bg-[#B59F84] text-white rounded-lg hover:bg-[#a08e77] transition-all duration-300 text-center font-medium">
                                        Update Donation
                                    </a>
                                @endif

                                    {{-- 1. MARK AS DONATED (Strict Check: Must be Available AND Approved) --}}
                                    @if ($donation->status === 'available' && $donation->approval_status === 'approved')
                                        <button type="button" onclick="openProofModal({{ $donation->id }})"
                                            class="w-full px-6 py-3 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 transition-all duration-300 font-medium">
                                            Mark as Donated
                                        </button>

                                        {{-- 2. PROOF LOGIC --}}
                                    @elseif ($donation->proof && $donation->verification_status === 'pending')
                                        <button type="button" disabled
                                            class="w-full px-6 py-3 bg-yellow-100 text-yellow-700 rounded-lg dark:bg-yellow-900 dark:text-yellow-300 transition-all duration-300 font-medium cursor-not-allowed opacity-75">
                                            Awaiting Admin Verification
                                        </button>
                                    @elseif ($donation->proof && $donation->verification_status === 'approved')
                                        <button type="button" disabled
                                            class="w-full px-6 py-3 bg-green-100 text-green-600 rounded-lg dark:bg-green-900 dark:text-green-300 transition-all duration-300 font-medium cursor-not-allowed">
                                            ✓ Verified | Points Redeemed
                                        </button>
                                    @elseif ($donation->proof && $donation->verification_status === 'rejected')
                                        <button type="button" onclick="openProofModal({{ $donation->id }})"
                                            class="w-full px-6 py-3 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-all duration-300 font-medium">
                                            Proof Rejected - Resubmit
                                        </button>

                                        {{-- 3. APPROVAL STATUS CHECKS (If not approved yet) --}}
                                    @elseif($donation->approval_status === 'pending')
                                        <button type="button" disabled
                                            class="w-full px-6 py-3 bg-yellow-50 text-yellow-600 rounded-lg dark:bg-yellow-900/20 dark:text-yellow-400 font-medium cursor-not-allowed border border-yellow-200 dark:border-yellow-800">
                                            Pending Approval
                                        </button>
                                    @elseif($donation->approval_status === 'rejected')
                                        <button type="button" disabled
                                            class="w-full px-6 py-3 bg-red-50 text-red-500 rounded-lg dark:bg-red-900/20 dark:text-red-400 font-medium cursor-not-allowed border border-red-200 dark:border-red-800">
                                            Rejected - Please Update
                                        </button>

                                    @endif
                                </div>
                            @endif

                            @if (Auth::check() && Auth::id() !== $donation->user_id && $donation->status === 'available')
                                <div
                                    class="w-full max-w-sm mt-4 bg-[#f8f4f0] dark:bg-gray-800 dark:text-gray-200 text-gray-800 p-4 rounded-lg shadow-md mx-auto border border-[#d9cbb6]">
                                    <div class="flex items-center gap-2 mb-2 dark:bg-gray-800 dark:text-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="#B59F84" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.625 12h6.75m-6.75 3h4.125M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-semibold text-sm text-[#5c4a3e] dark:text-gray-200">Send
                                            donor a message</span>
                                    </div>
                                    <textarea
                                        class="w-full bg-white border border-[#d9cbb6] dark:bg-gray-800 dark:text-gray-200 text-gray-700 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B59F84] resize-none"
                                        rows="2" readonly>Hi {{ $donation->user->fname }}, is this still available?</textarea>
                                    <a href="{{ route('private.chat', $donation->user->id) }}?auto_message=1&donation_id={{ $donation->id }}&donation_name={{ urlencode($donation->name) }}&donation_image={{ urlencode($donation->first_image) }}"
                                        class="block w-full bg-[#B59F84] text-white text-center py-2.5 rounded-md font-medium hover:bg-[#a08e77] transition-all duration-300">
                                        Send
                                    </a>
                                </div>
                            @endif

                            <div id="proofModal"
                                class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
                                <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl relative">
                                    <button onclick="closeProofModal()"
                                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl">
                                        ×
                                    </button>

                                    <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">
                                        Upload Donation Proof
                                    </h2>

                                    <form id="proofForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-4">
                                            <label for="proof" class="block text-gray-700 font-medium mb-2">
                                                Upload Proof Image
                                            </label>
                                            <input type="file" name="proof" id="proof" accept="image/*"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#B59F84] focus:outline-none"
                                                required>
                                        </div>

                                        <div class="flex justify-end mt-5">
                                            <button type="button" onclick="closeProofModal()"
                                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg mr-2">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="bg-[#B59F84] hover:bg-[#a08e77] text-white px-4 py-2 rounded-lg">
                                                Submit Proof
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-2/3 flex flex-col gap-8">
                    {{-- ... (The rest of your Right Side Content: Map, Comments, More Donations) ... --}}
                    {{-- I have omitted the rest of the file as no changes were required below this point, 
                         but make sure to keep your existing Google Maps, Comments, and Scripts sections. --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="relative h-36 bg-center bg-cover"
                            style="background-image: url('{{ asset('images/Rectangle 99.png') }}');">
                            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/30"></div>
                        </div>

                        <div class="relative bg-[#E1D5B6] dark:bg-gray-800 p-6">
                            <div
                                class="absolute -top-10 left-6 w-20 h-20 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-md">
                                @if ($donation->user->profile_pic)
                                    <img src="{{ Storage::disk('s3')->url($donation->user->profile_pic) }}"
                                        alt="Profile Picture" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/default-profile.jpg') }}"
                                        alt="Default Profile Picture" class="w-full h-full object-cover">
                                @endif
                            </div>

                            <div class="flex items-start justify-between pt-10">
                                <div class="flex-1">
                                    <div class="donation-card">
                                        <x-user-name-badge :user="$donation->user" />
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <div class="flex text-yellow-400">
                                            <span>★★★★★</span>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">(5)</span>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 ml-4">
                                    @if (Auth::check() && Auth::id() !== $donation->user->id)
                                        <a href="{{ route('private.chat', $donation->user->id) }}"
                                            class="px-5 py-2.5 bg-white dark:bg-gray-700 text-[#B59F84] dark:text-[#E1D5B6] rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-300 text-sm font-medium">
                                            Message
                                        </a>
                                    @endif
                                    <a href="{{ route('profile.show', $donation->user->id) }}"
                                        class="px-5 py-2.5 bg-white dark:bg-gray-700 text-[#B59F84] dark:text-[#E1D5B6] rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-300 text-sm font-medium">
                                        Visit Profile
                                    </a>
                                </div>
                            </div>

                            @if (Auth::id() !== $donation->user_id)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('reports.create', $donation->user->id) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 9v2m0 4h.01M5.455 4.455a2.836 2.836 0 012-1.455h9.09a2.836 2.836 0 012 1.455l3.182 5.455a2.836 2.836 0 010 2.182L18.545 17.09a2.836 2.836 0 01-2 1.455H7.455a2.836 2.836 0 01-2-1.455L2.273 12.09a2.836 2.836 0 010-2.182L5.455 4.455z" />
                                        </svg>
                                        Report User
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-1 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#B59F84]" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Location</h3>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Address:</span>
                                {{ $donation->barangay->name ?? 'N/A' }}, Cebu City, Cebu 6000
                            </p>
                        </div>

                        <div class="rounded-lg overflow-hidden shadow-lg border border-gray-200 dark:border-gray-700">
                            <div id="google-map-container"
                                class="w-full h-64 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                @if ($donation->barangay && $donation->barangay->name)
                                    <iframe id="location-map" width="100%" height="100%" style="border:0;"
                                        loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                        src="https://maps.google.com/maps?q={{ urlencode($donation->barangay->name . ', Cebu City, Cebu, Philippines') }}&z=15&output=embed">
                                    </iframe>
                                @else
                                    <div class="text-center text-gray-500 dark:text-gray-400 p-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 11111.314 0z" />
                                        </svg>
                                        <p class="text-sm">Location information not available</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex gap-3">
                            @if ($donation->barangay && $donation->barangay->name)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($donation->barangay->name . ', Cebu City, Cebu, Philippines') }}"
                                    target="_blank"
                                    class="flex-1 bg-[#B59F84] text-white text-center py-2.5 rounded-lg hover:bg-[#a08e77] transition-all duration-300 font-medium text-sm">
                                    Open in Google Maps
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="bg-[#F4F2ED] dark:bg-gray-800 rounded-xl p-10 shadow-md">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Comments</h3>

                        <div id="comments-container" class="space-y-4 max-h-80 overflow-y-auto pr-2">
                            @forelse($donation->comments as $comment)
                                <div class="comment-item bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm"
                                    data-comment-id="{{ $comment->id }}" id="comment-{{ $comment->id }}">
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 overflow-hidden bg-[#B59F84] flex items-center justify-center">
                                                @if ($comment->user->profile_pic)
                                                    <img src="{{ Storage::disk('s3')->url($comment->user->profile_pic) }}"
                                                        alt="{{ $comment->user->fname }}'s Profile Picture"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <img src="{{ asset('images/default-profile.jpg') }}"
                                                        alt="Default Profile Picture"
                                                        class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex justify-between items-start mb-1">
                                                <div>
                                                    <a href="{{ route('profile.show', $comment->user->id) }}"
                                                        class="hover:underline">
                                                        <x-user-name-badge :user="$comment->user" />
                                                    </a>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </span>
                                                </div>

                                                @if (Auth::id() === $comment->user_id)
                                                    <div class="relative">
                                                        <button type="button"
                                                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600"
                                                            onclick="toggleDropdown({{ $comment->id }})">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 text-gray-600 dark:text-gray-200"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                        </button>
                                                        <div id="dropdown-{{ $comment->id }}"
                                                            class="absolute right-0 mt-1 w-28 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow z-10 hidden">
                                                            <button type="button"
                                                                onclick="toggleEditForm({{ $comment->id }})"
                                                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                                Edit
                                                            </button>
                                                            <button type="button"
                                                                onclick="deleteComment({{ $comment->id }})"
                                                                class="w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div id="comment-content-{{ $comment->id }}"
                                                class="text-gray-800 dark:text-gray-200 mb-2">
                                                {{ $comment->content }}
                                            </div>
                                            <div
                                                class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                <button onclick="toggleLike({{ $comment->id }})"
                                                    class="flex items-center gap-1 hover:text-[#B59F84] transition-colors duration-200 {{ $comment->userLikes->count() > 0 ? 'text-[#B59F84]' : 'text-gray-500' }}"
                                                    id="like-btn-{{ $comment->id }}">
                                                    <svg class="w-4 h-4"
                                                        fill="{{ $comment->userLikes->count() > 0 ? 'currentColor' : 'none' }}"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        id="like-count-{{ $comment->id }}">{{ $comment->likes_count }}</span>
                                                </button>
                                                <button
                                                    onclick="startReply({{ $comment->id }}, '{{ addslashes($comment->user->fname . ' ' . $comment->user->lname) }}')"
                                                    class="flex items-center gap-1 hover:text-[#B59F84] transition-colors duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                                        </path>
                                                    </svg>
                                                    Reply
                                                </button>
                                                @if ($comment->replies && $comment->replies->count() > 0)
                                                    <button onclick="toggleReplies({{ $comment->id }})"
                                                        class="text-[#B59F84] hover:underline">
                                                        {{ $comment->replies->count() }}
                                                        {{ $comment->replies->count() == 1 ? 'reply' : 'replies' }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (Auth::id() === $comment->user_id)
                                        <form id="inline-edit-form-{{ $comment->id }}"
                                            action="{{ route('comments.update', $comment->id) }}" method="POST"
                                            class="inline-edit-form hidden mt-2 bg-gray-100 dark:bg-gray-600 p-3 rounded-lg"
                                            data-id="{{ $comment->id }}">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" rows="2"
                                                class="w-full border rounded p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ old('content', $comment->content) }}</textarea>
                                            <div class="flex gap-2 mt-2">
                                                <button type="submit"
                                                    class="px-3 py-1 bg-[#B59F84] text-white rounded text-sm hover:bg-[#a08e77] transition-all duration-200">Save</button>
                                                <button type="button" onclick="cancelEdit({{ $comment->id }})"
                                                    class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">Cancel</button>
                                            </div>
                                        </form>
                                    @endif

                                    <div id="replies-{{ $comment->id }}"
                                        class="hidden ml-4 mt-3 space-y-3 border-l-2 border-gray-200 dark:border-gray-600 pl-4">
                                        @foreach ($comment->replies as $reply)
                                            <div class="reply-item flex gap-3" data-comment-id="{{ $reply->id }}"
                                                id="reply-{{ $reply->id }}"
                                                data-parent-id="{{ $reply->parent_id }}">

                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-8 h-8 bg-[#B59F84] rounded-full border-2 border-white dark:border-gray-800 overflow-hidden flex items-center justify-center">
                                                        @if ($reply->user->profile_pic)
                                                            <img src="{{ asset('storage/' . $reply->user->profile_pic) }}"
                                                                alt="{{ $reply->user->fname }}'s Profile Picture"
                                                                class="w-full h-full object-cover">
                                                        @else
                                                            <img src="{{ asset('images/default-profile.jpg') }}"
                                                                alt="Default Profile Picture"
                                                                class="w-full h-full object-cover">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex-1">
                                                    <div>
                                                        <a href="{{ route('profile.show', $reply->user->id) }}"
                                                            class="hover:underline">
                                                            <x-user-name-badge :user="$reply->user" />
                                                        </a>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>

                                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                                        {{ $reply->content }}</p>

                                                    <div
                                                        class="mt-2 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                                        <button onclick="toggleLike({{ $reply->id }})"
                                                            class="flex items-center gap-1 hover:text-[#B59F84] transition-colors duration-200 {{ $reply->userLikes->count() > 0 ? 'text-[#B59F84]' : 'text-gray-500' }}"
                                                            id="like-btn-{{ $reply->id }}">
                                                            <svg class="w-3 h-3"
                                                                fill="{{ $reply->userLikes->count() > 0 ? 'currentColor' : 'none' }}"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                                </path>
                                                            </svg>
                                                            <span
                                                                id="like-count-{{ $reply->id }}">{{ $reply->likes_count }}</span>
                                                        </button>

                                                        <button
                                                            onclick="startReply({{ $reply->id }}, '{{ addslashes($reply->user->fname . ' ' . $reply->user->lname) }}')"
                                                            class="hover:text-[#B59F84] transition-colors duration-200">
                                                            Reply
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 dark:text-gray-400 text-sm py-4 text-center">No comments yet.
                                    Be the first to comment!</p>
                            @endforelse
                        </div>

                        @auth
                            <form id="comment-form" action="{{ route('comments.store') }}" method="POST"
                                class="mt-6">
                                @csrf
                                <input type="hidden" name="donation_id" value="{{ $donation->id }}">
                                <input type="hidden" name="parent_id" id="parent_id" value="">
                                <div
                                    class="relative flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full max-w-xl bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-200 dark:border-gray-600 shadow-md">
                                    <div class="relative w-full flex items-center">
                                        <textarea name="content" id="comment-content" placeholder="Write a comment..."
                                            class="mentionable flex-1 w-full resize-none overflow-hidden rounded-lg px-4 py-2 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#B59F84] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 pr-10"
                                            rows="2" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'; handleCommentInput()"
                                            required></textarea>
                                        <button type="submit"
                                            class="absolute right-2 bottom-2 bg-[#B59F84] text-white font-semibold px-3 py-2 rounded-lg shadow hover:bg-[#a08e77] transition-all duration-300 ease-in-out md:static md:ml-3 md:bottom-auto md:right-auto md:w-auto">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <button type="button" id="reply-cancel-btn" onclick="cancelReply()"
                                        class="hidden ml-2 text-[#B59F84] hover:underline">Cancel</button>
                                </div>

                                <div id="comment-error" class="text-red-500 mt-2 text-sm hidden"></div>

                                <div id="reply-indicator"
                                    class="hidden mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <span id="replying-to" class="font-medium"></span>
                                </div>
                            </form>
                        @else
                            <p class="mt-3 text-gray-600 dark:text-gray-400">
                                <a href="{{ route('login') }}" class="text-[#B59F84] hover:underline">Login</a> to
                                comment.
                            </p>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($moreDonations->count())
        <div class="py-6 bg-white dark:bg-gray-900 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
                        More from {{ $donation->user->fname }}
                    </h2>
                </div>
                <div class="rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-6">
                        <div
                            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                            @foreach ($moreDonations as $donationItem)
                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-200 border border-[#D9D9D9] dark:border-gray-700">
                                    <a href="{{ route('donations.show', $donationItem->id) }}" class="block h-full">
                                        <div
                                            class="absolute top-1 left-1 z-10 bg-[#D9D9D9] text-gray-700 text-[10px] sm:text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full">
                                            Donation
                                        </div>
                                        <div class="relative aspect-square overflow-hidden">
                                            <img src="{{ $donationItem->first_image }}"
                                                alt="{{ $donationItem->name }}"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                                            <div
                                                class="absolute inset-0 bg-gray-800 bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                <span
                                                    class="bg-white text-gray-800 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium">
                                                    Quick view
                                                </span>
                                            </div>
                                        </div>

                                        <div class="p-2 sm:p-3">
                                            <div class="flex justify-between items-start">
                                                <h3
                                                    class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white transition-colors truncate max-w-[70%]">
                                                    {{ $donationItem->name }}
                                                </h3>
                                                <span
                                                    class="text-[10px] sm:text-xs font-medium px-1 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                                    {{ $donationItem->size ?? 'L' }}
                                                </span>
                                            </div>

                                            <p
                                                class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                                                {{ $donationItem->category->name ?? 'No Category' }}
                                            </p>
                                            <p
                                                class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                                                <i>{{ $donationItem->barangay->name ?? 'N/A' }}, Cebu City</i>
                                            </p>

                                            <div class="flex justify-between items-center mt-1">
                                                <p class="text-xs sm:text-sm font-bold text-gray-700">
                                                    For Donation
                                                </p>

                                                <button
                                                    class="favorite-btn text-gray-400 hover:text-red-500 focus:outline-none transition-colors"
                                                    data-id="{{ $donationItem->id }}" type="button"
                                                    onclick="event.preventDefault(); event.stopPropagation();">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 sm:h-5 sm:w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="py-6 bg-white dark:bg-gray-900 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center p-6 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        No other donations from <strong>{{ $donation->user->fname }}</strong> yet.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Keep your scripts intact below --}}
    <script>
        // Participants list for @mentions
        window.commentParticipants = [{
                id: {{ $donation->user->id }},
                name: '{{ addslashes($donation->user->fname . ' ' . $donation->user->lname) }}'
            },
            @php $added = collect([$donation->user->id]); @endphp
            @foreach ($donation->comments as $c)
                @if (!$added->contains($c->user->id))
                    {
                        id: {{ $c->user->id }},
                        name: '{{ addslashes($c->user->fname . ' ' . $c->user->lname) }}'
                    },
                    @php $added->push($c->user->id); @endphp
                @endif
                @foreach ($c->replies as $r)
                    @if (!$added->contains($r->user->id))
                        {
                            id: {{ $r->user->id }},
                            name: '{{ addslashes($r->user->fname . ' ' . $r->user->lname) }}'
                        },
                        @php $added->push($r->user->id); @endphp
                    @endif
                @endforeach
            @endforeach
        ];

        (function setupMentions() {
            const suggestions = document.createElement('div');
            suggestions.id = 'mention-suggestions';
            suggestions.className =
                'hidden z-40 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow max-h-48 overflow-auto w-64';
            suggestions.style.position = 'absolute';
            document.body.appendChild(suggestions);

            let activeTextarea = null;

            function filterParticipants(query) {
                const q = query.toLowerCase();
                return window.commentParticipants.filter(p => p.name.toLowerCase().includes(q)).slice(0, 8);
            }

            function positionSuggestions() {
                if (!activeTextarea) return;
                const rect = activeTextarea.getBoundingClientRect();
                const scrollY = window.scrollY || document.documentElement.scrollTop;
                const scrollX = window.scrollX || document.documentElement.scrollLeft;
                suggestions.style.left = (scrollX + rect.left + 8) + 'px';
                suggestions.style.top = (scrollY + rect.top - 6) + 'px';
            }

            function renderSuggestions(list) {
                if (!list.length) {
                    hideSuggestions();
                    return;
                }
                suggestions.innerHTML = list.map(p =>
                    `<button type="button" data-name="${p.name}" class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 text-sm">@${p.name}</button>`
                ).join('');
                suggestions.classList.remove('hidden');
                positionSuggestions();
            }

            function hideSuggestions() {
                suggestions.classList.add('hidden');
                suggestions.innerHTML = '';
            }

            document.addEventListener('focusin', (e) => {
                if (e.target && e.target.matches('textarea.mentionable')) {
                    activeTextarea = e.target;
                }
            });

            document.addEventListener('input', (e) => {
                if (!(e.target && e.target.matches('textarea.mentionable'))) return;
                activeTextarea = e.target;
                const caretPos = activeTextarea.selectionStart;
                const val = activeTextarea.value.substring(0, caretPos);
                const match = val.match(/(^|\s)@([\w\s]{0,30})$/);
                if (match) {
                    const query = match[2].trim();
                    renderSuggestions(query ? filterParticipants(query) : window.commentParticipants.slice(0,
                        6));
                } else {
                    hideSuggestions();
                }
                positionSuggestions();
            });

            suggestions.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-name]');
                if (!btn || !activeTextarea) return;
                const name = btn.getAttribute('data-name');
                const caret = activeTextarea.selectionStart;
                const before = activeTextarea.value.substring(0, caret);
                const after = activeTextarea.value.substring(caret);
                const replaced = before.replace(/(^|\s)@([\w\s]{0,30})$/, `$1@${name} `);
                activeTextarea.value = replaced + after;
                const newCaret = replaced.length;
                activeTextarea.setSelectionRange(newCaret, newCaret);
                activeTextarea.focus();
                hideSuggestions();
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#mention-suggestions') && !e.target.closest('textarea.mentionable'))
                    hideSuggestions();
            });

            document.addEventListener('DOMContentLoaded', function() {
                new Swiper(".mySwiper", {
                    loop: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    effect: "slide", // you can use "fade" or "coverflow"
                    speed: 800,
                });
            });
        })();

        //upload proof script
        function openProofModal(donationId) {
            const modal = document.getElementById('proofModal');
            const form = document.getElementById('proofForm');
            form.action = `/donations/${donationId}/mark-as-donated`;
            modal.classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
        }
    </script>


    <style>
        /* Keep your existing styles */
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .comment-item:hover .line-clamp-3 {
            -webkit-line-clamp: unset;
            display: block;
        }

        #reply-indicator {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #replying-to {
            font-weight: 500;
            color: #6c757d;
        }
    </style>
</x-app-layout>
