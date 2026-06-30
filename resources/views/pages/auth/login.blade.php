<x-layouts::auth :title="__('Log in')">
    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-500 mb-1 ml-2">{{ __('Username or Email Address') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                class="w-full px-5 py-2.5 bg-white border border-transparent rounded-full focus:ring-2 focus:ring-[#2C5EAD] focus:border-transparent shadow-sm text-slate-700 placeholder-slate-400">
            @error('email')
                <p class="mt-1 ml-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-500 mb-1 ml-2">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-5 py-2.5 bg-white border border-transparent rounded-full focus:ring-2 focus:ring-[#2C5EAD] focus:border-transparent shadow-sm text-slate-700 placeholder-slate-400">
            @error('password')
                <p class="mt-1 ml-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end mt-2">
            <button type="submit" class="w-full px-8 py-2.5 bg-[#2C5EAD] hover:bg-[#204a8e] text-white font-medium rounded-full shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-[#2C5EAD] focus:ring-offset-2 focus:ring-offset-[#e5e7eb]">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-layouts::auth>
