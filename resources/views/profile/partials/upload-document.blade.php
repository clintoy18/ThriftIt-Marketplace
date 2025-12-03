<section x-data="{ showUpload: false }" class="space-y-4">

    <!-- Compact Verification Status Badge/Button -->
    <div
        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">

            <!-- Icon -->
            <div class="flex-shrink-0">
                @if ($user->verification_status === 'pending')
                    <div
                        class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 animate-pulse" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @elseif($user->verification_status === 'approved')
                    <div
                        class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @else
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Text -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    @if ($user->verification_status === 'pending')
                        Verification Pending
                    @elseif($user->verification_status === 'approved')
                        Account Verified
                    @else
                        Identity Verification
                    @endif
                </h4>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    @if ($user->verification_status === 'pending')
                        Under review (1–3 working days)
                    @elseif($user->verification_status === 'approved')
                        You're verified — buyers trust your profile
                    @else
                        Verify now to unlock selling features and get trusted
                    @endif
                </p>
            </div>
        </div>

        <!-- Action Button -->
        @if ($user->verification_status !== 'pending' && $user->verification_status !== 'approved')
            <button type="button" @click="showUpload = !showUpload"
                class="px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Get Verified
            </button>
        @endif
    </div>

    <!-- Upload Form -->
    @if ($user->verification_status !== 'pending' && $user->verification_status !== 'approved')
        <div x-show="showUpload" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="p-5 border border-green-200 dark:border-green-800 rounded-lg bg-white dark:bg-gray-800">

            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Upload Valid ID</h3>

            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                Accepted government-issued IDs:
                <span class="font-medium">PhilSys National ID</span>, Passport, Driver’s License,
                SSS/GSIS UMID, PRC License, Voter’s ID, Postal ID, PhilHealth ID, TIN ID,
                Senior Citizen ID, PWD ID, Student ID (School-issued), Company ID (Valid),
                Barangay ID (recent), Firearm License ID, OFW/OWWA ID, Seaman’s Book,
                <span class="font-medium">or any other valid government-issued identification card.</span>
                <br>
                <span class="font-semibold">Max file size: 2MB</span>
            </p>

            <form action="{{ route('profile.verification.upload') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <div>
                    <input type="file" name="verification_document" id="verification_document" accept="image/*,.pdf"
                        class="block w-full text-sm text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-400">

                    @error('verification_document')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showUpload = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    @endif
</section>
