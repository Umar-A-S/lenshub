<x-guest-layout>

    <div class="mb-4">
        <a href="{{ url('/') }}"
            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="me-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Beranda
        </a>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Identifier: Email / Username / Nomor WA -->
        <div>
            <x-input-label for="identifier" value="Email / Username / Nomor WA" />
            <x-text-input id="identifier" class="mt-1 block w-full" type="text" name="identifier" :value="old('identifier')"
                required autofocus autocomplete="username" placeholder="Email, username, atau nomor WA" />
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
        <div class="mt-4 flex items-center justify-center">
            @if (Route::has('register'))
                <a class="rounded-md text-sm text-gray-600 underline focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    href="{{ route('register') }}">
                    Belum punya akun? <span class="font-semibold text-blue-600 hover:text-blue-950">Daftar</span>
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
