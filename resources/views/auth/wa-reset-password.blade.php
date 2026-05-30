<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Masukkan kode OTP yang dikirim ke WhatsApp
        @if ($masked ?? null)
            <strong>{{ $masked }}</strong>
        @endif
        dan password baru Anda.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.wa.reset') }}">
        @csrf

        <!-- Kode OTP -->
        <div>
            <x-input-label for="otp" value="Kode OTP (6 digit)" />
            <x-text-input
                id="otp"
                class="block mt-1 w-full tracking-widest text-center text-lg font-mono"
                type="text"
                name="otp"
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]{6}"
                required
                autofocus
                placeholder="_ _ _ _ _ _" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <!-- Password Baru -->
        <div class="mt-4">
            <x-input-label for="password" value="Password Baru" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('password.request') }}"
               class="underline text-sm text-gray-600 hover:text-gray-900">
                Kirim ulang OTP
            </a>
            <x-primary-button>
                Reset Password
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
