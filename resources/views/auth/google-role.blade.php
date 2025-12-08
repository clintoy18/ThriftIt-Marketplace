<x-guest-layout containerClass="max-w-[413px]" reverseColumns="true">
    <div class="max-w-[300px] mx-auto mt-16">
        <h1 class="text-3xl font-bold text-center mb-4">Select Your Role</h1>
        <form method="POST" action="{{ route('google.role.assign') }}">
            @csrf
            <select name="role" class="w-full h-[40px] rounded-lg border px-3" required>
                <option value="" disabled selected>Select Role</option>
                <option value="0">Thrifter</option>
                <option value="1">Upcycler</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <button type="submit" class="w-full mt-4 py-2 bg-green-600 text-white rounded-lg">
                Continue
            </button>
        </form>
    </div>
</x-guest-layout>
