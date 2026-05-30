<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Masukkan email, username, atau nomor WhatsApp yang terdaftar.
        Kode OTP akan dikirimkan ke nomor WA Anda.
    </div>

    @if (session('wa_otp_sent'))
        <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded px-4 py-3">
            {{ session('wa_masked', 'Jika data ditemukan, kode OTP telah dikirim ke WhatsApp Anda.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.wa.send') }}">
        @csrf

        <!-- Identifier -->
        <div>
            <x-input-label for="identifier" value="Email / Username / Nomor WA" />
            <x-text-input
                id="identifier"
                class="block mt-1 w-full"
                type="text"
                name="identifier"
                :value="old('identifier')"
                required
                autofocus
                placeholder="Masukkan email, username, atau nomor WA" />
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('login') }}"
               class="underline text-sm text-gray-600 hover:text-gray-900 me-4">
                Kembali ke login
            </a>
            <x-primary-button>
                Kirim OTP via WA
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
