<section x-data="{ 
    showUpload: {{ $errors->any() ? 'true' : 'false' }}, 
    showTerms: false,
    frontPreview: null,
    backPreview: null
}" class="space-y-4">

    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                @if ($user->verification_status === 'pending')
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                @elseif($user->verification_status === 'approved')
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                @else
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                @endif
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->fname }} {{ $user->lname }}</p>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    @if ($user->verification_status === 'pending') Verification Pending
                    @elseif($user->verification_status === 'approved') Account Verified
                    @else Identity Verification @endif
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    @if ($user->verification_status === 'pending') Under review
                    @elseif($user->verification_status === 'approved') Verified Profile
                    @else Upload front and back of ID @endif
                </p>
            </div>
        </div>
        @if ($user->verification_status !== 'pending' && $user->verification_status !== 'approved')
            <button type="button" @click="showUpload = !showUpload" class="px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors">Get Verified</button>
        @endif
    </div>

    @if ($user->verification_status !== 'pending' && $user->verification_status !== 'approved')
        <div x-show="showUpload" x-cloak class="p-5 border border-green-200 dark:border-green-800 rounded-lg bg-white dark:bg-gray-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Upload Identification</h3>

            <div class="text-xs text-gray-600 dark:text-gray-400 mb-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                <span class="font-semibold block mb-2 text-gray-800 dark:text-gray-200">Accepted government-issued IDs:</span>
                
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 list-disc list-inside marker:text-gray-400">
                    <li>PhilSys National ID</li>
                    <li>Passport</li>
                    <li>Driver's License</li>
                    <li>SSS/GSIS UMID</li>
                    <li>PRC License</li>
                    <li>Voter's ID</li>
                    <li>Postal ID</li>
                    <li>PhilHealth ID</li>
                    <li>TIN ID</li>
                    <li>Senior Citizen ID</li>
                    <li>PWD ID</li>
                    <li>Student ID (School-issued)</li>
                    <li>Company ID (Valid)</li>
                    <li>Barangay ID (recent)</li>
                    <li>Firearm License ID</li>
                    <li>OFW/OWWA ID</li>
                    <li>Seaman's Book</li>
                    <li>Any valid Govt ID</li>
                </ul>

                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                    <span class="font-semibold text-green-600 dark:text-green-400">Max file size: 2MB per file</span>
                </div>
            </div>

            <form action="{{ route('profile.verification.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">
                            Front of ID <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="verification_document" accept="image/*,.pdf" required
                            @change="frontPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="block w-full text-sm text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-400">
                        
                        @error('verification_document') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                        <div x-show="frontPreview" class="mt-2">
                             <img :src="frontPreview" class="h-32 w-auto rounded-lg border border-gray-200 dark:border-gray-700 object-contain">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">
                            Back of ID <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="verification_document_back" accept="image/*,.pdf" required
                            @change="backPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="block w-full text-sm text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-400">
                        
                        @error('verification_document_back') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                        <div x-show="backPreview" class="mt-2">
                             <img :src="backPreview" class="h-32 w-auto rounded-lg border border-gray-200 dark:border-gray-700 object-contain">
                        </div>
                    </div>
                </div>

                <div class="flex items-start space-x-2 pt-2">
                    <input type="checkbox" name="terms" id="terms" class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:bg-gray-700">
                    <label for="terms" class="text-sm text-gray-700 dark:text-gray-300">
                        I agree to the <button type="button" @click="showTerms = true" class="text-green-600 hover:underline">Terms and Conditions</button>.
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showUpload = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg">Submit</button>
                </div>
            </form>
        </div>

        <div x-show="showTerms" x-cloak 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 overflow-y-auto max-h-[80vh] relative" @click.away="showTerms = false">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Terms and Conditions</h2>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                    By submitting your verification document, you agree to:
                </p>
                <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <li>Provide accurate and authentic identification documents.</li>
                    <li>Allow the platform to temporarily store your document for verification purposes only.</li>
                    <li>Understand that providing false or fraudulent documents may lead to account suspension or banning.</li>
                    <li>Your personal information will be handled in accordance with our privacy policy.</li>
                    <li>The platform reserves the right to reject verification submissions that do not meet the criteria.</li>
                </ul>
                <div class="mt-4 flex justify-end">
                    <button type="button" @click="showTerms = false"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</section>