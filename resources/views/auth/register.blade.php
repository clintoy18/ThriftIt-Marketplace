<x-guest-layout containerClass="max-w-[413px]" reverseColumns="true">
    {{-- Removed wrapper for smaller form size --}}
    <div class="max-w-[300px] mx-auto">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="flex flex-col items-center mt-8">
                <h1 class="text-3xl font-poppins font-bold text-black dark:text-black">
                    Sign Up
                </h1>
            </div>

            <!-- Name -->
            <div class="flex flex-col items-center mt-8">
                <x-input-label for="fname" />
                <x-text-input id="fname"
                    class="w-[295px] h-[40px] t-[405px] placeholder:text-[15px] placeholder:leading-[24px] placeholder:text-base placeholder:font-poppins"
                    type="text" name="fname" placeholder="First Name" :value="old('fname')" required autofocus
                    autocomplete="fname" />
                <x-input-error :messages="$errors->get('fname')" class="mt-2" />
            </div>

            <div class="flex flex-col items-center mt-4">
                <x-input-label for="lname" />
                <x-text-input id="lname"
                    class="w-[295px] h-[40px] t-[405px] placeholder:text-[15px] placeholder:leading-[24px] placeholder:text-base placeholder:font-poppins"
                    type="text" name="lname" placeholder="Last Name" :value="old('lname')" required
                    autocomplete="lname" />
                <x-input-error :messages="$errors->get('lname')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="flex flex-col items-center mt-4">
                <x-input-label for="email" />
                <x-text-input id="email"
                    class="w-[295px] h-[40px] t-[405px] placeholder:text-[15px] placeholder:leading-[24px] placeholder:text-base placeholder:font-poppins"
                    type="text" name="email" placeholder="Email" :value="old('email')" required
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Role Selection -->
            <div class="flex flex-col items-center mt-4">
                <x-input-label for="role" />

                <select id="role" name="role"
                    class="w-[295px] h-[40px] t-[405px] text-[15px] leading-[24px] text-base font-poppins
           focus:border-[#B59F84]  focus:ring-2 focus:ring-[#B59F84] rounded-full shadow-sm"
                    required>
                    <option class="text-gray-400 text-[15px] leading-[24px] text-base font-poppins" value=""
                        disabled {{ old('role') === null || old('role') === '' ? 'selected' : '' }}>
                        Select Role
                    </option>
                    <option value="0" {{ old('role') == '0' ? 'selected' : '' }}>Thrifter</option>
                    <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Upcycler</option>
                </select>

                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="flex flex-col items-center mt-4">
                <x-input-label for="password" />
                <div class="relative w-[295px]">
                    <x-text-input id="password"
                        class="w-full h-[40px] placeholder:text-[15px] placeholder:leading-[24px] placeholder:text-base placeholder:font-poppins pr-10"
                        type="password" name="password" placeholder="Password" :value="old('Password')" required
                        autocomplete="new-password" />
                    <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col items-center mt-4">
                <x-input-label for="password_confirmation" />
                <div class="relative w-[295px]">
                    <x-text-input id="password_confirmation"
                        class="w-full h-[40px] placeholder:text-[15px] placeholder:leading-[24px] placeholder:text-base placeholder:font-poppins pr-10"
                        type="password" name="password_confirmation" placeholder="Confirm Password" :value="old('Confirm Password')"
                        required autocomplete="new-password" />
                    <button type="button" onclick="togglePassword('password_confirmation')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-col items-center mt-7">
                <button type="submit"
                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-[25px] shadow-sm text-white bg-[#B59F84] hover:bg-[#a08e77] hover:scale-105 transition-all duration-200 ">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('Register') }}
                </button>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-[#B59F84] hover:text-[#a08e77] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#B59F84] focus:ring-offset-2"
                    href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-eye');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</x-guest-layout>
