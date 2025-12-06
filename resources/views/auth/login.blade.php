<x-guest-layout containerClass="max-w-[400px] bg-[#F2F8FC]">

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="max-w-[377px] mx-auto py-6">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Title -->
            <div class="mb-6 flex items-center justify-center">
                <h1 class="text-3xl font-poppins font-semibold text-black">Login</h1>
            </div>

            <!-- Email Address -->
            <div class="flex flex-col items-center">
                <x-input-label for="email" />

                <x-text-input id="email"
                    class="w-[295px] h-[40px] placeholder:text-[15px] placeholder:font-poppins"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email or Username"
                    required autofocus autocomplete="username" />

                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="flex flex-col items-center mt-4">
                <x-input-label for="password" />

                <x-text-input id="password"
                    class="w-[295px] h-[40px] placeholder:text-[15px] placeholder:font-poppins"
                    type="password"
                    name="password"
                    placeholder="Password"
                    required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Login Button -->
            <div class="flex flex-col items-center mt-6">
                <button type="submit"
                    class="w-[295px] h-[36px] rounded-[25px] text-sm font-medium shadow-sm text-white bg-[#B59F84] hover:bg-[#a08e77] hover:scale-105 transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-1"></i>
                    {{ __('Log in') }}
                </button>
            </div>

            <!-- Google Login -->
            <div class="flex flex-col items-center mt-4">
                <a href="{{ route('google.redirect') }}"
                    class="w-[295px] h-[40px] bg-white border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-[8px] flex items-center justify-center gap-2 transition-all duration-300 shadow-sm hover:shadow-md">

                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>

                    <span>Continue with Google</span>
                </a>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mt-5 ml-[40px]">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none gap-2">
                    <input id="remember_me" type="checkbox"
                        class="h-5 w-5 rounded border-gray-300 text-[#B59F84] focus:ring-[#B59F84] transition-all duration-150"
                        name="remember">
                    <span class="text-base text-gray-700 font-medium">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Sign Up -->
            <div class="flex items-center ml-[40px] mt-4 gap-1">
                <span class="text-black font-poppins">{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}"
                    class="underline text-sm text-[#634600] hover:text-[#a08e77] transition-colors duration-200">
                    <i class="fas fa-user-plus mr-1"></i>
                    <span class="italic">{{ __('Sign Up') }}</span>
                </a>
            </div>

            <!-- Forgot Password -->
            <div class="flex justify-end mt-4 mr-[40px]">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-[#634600] hover:text-[#a08e77] transition-colors duration-200"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

        </form>
    </div>
</x-guest-layout>
