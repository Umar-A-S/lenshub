<section x-data="profileOtp()" x-init="init()">

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Lengkapi profil Anda agar bisa melakukan pemesanan. Username hanya bisa diubah <strong>1 kali</strong>.
        </p>
    </header>

    {{-- ── Alert profil tidak lengkap ───────────────────────────────── --}}
    @if (session('profil_tidak_lengkap'))
        <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4">
            <p class="mb-1 text-sm font-semibold text-amber-800">⚠️ Profil belum lengkap. Selesaikan langkah berikut
                sebelum memesan:</p>
            <ul class="list-inside list-disc space-y-1 text-sm text-amber-700">
                @foreach (session('profil_tidak_lengkap') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Form update profil ────────────────────────────────────────── --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Nama --}}
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Username (kunci setelah 1x diisi) --}}
        <div>
            <x-input-label for="username" value="Username" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-400">@</span>
                <x-text-input id="username" name="username" type="text"
                    class="{{ $user->username_changed ? 'bg-gray-100 cursor-not-allowed' : '' }} block w-full pl-7"
                    :value="old('username', $user->username)" :readonly="$user->username_changed" placeholder="contoh: budi.santoso" />
            </div>
            @if ($user->username_changed)
                <p class="mt-1 text-xs text-amber-600">🔒 Username sudah dikunci dan tidak bisa diubah lagi.</p>
            @else
                <p class="mt-1 text-xs text-gray-500">Hanya huruf kecil, angka, titik, dan underscore. Hanya bisa diubah
                    1 kali.</p>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <p class="mt-1 text-xs text-gray-500">📧 Email digunakan sebagai opsi login dan penerima rekap sewa otomatis.</p>
        </div>

        {{-- Nomor WhatsApp --}}
        <div>
            <x-input-label for="phone" value="Nomor WhatsApp" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)"
                placeholder="08xxxxxxxxxx" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />

            {{-- Status verifikasi WA --}}
            @if ($user->phone && $user->hasVerifiedPhone())
                <p class="mt-1 text-xs font-medium text-green-600">✅ Nomor WhatsApp sudah diverifikasi.</p>
            @elseif ($user->phone)
                <p class="mt-1 text-xs font-medium text-red-500">⚠️ Nomor WA belum diverifikasi. Simpan profil lalu
                    verifikasi di bawah.</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan Profil</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ── Bagian Verifikasi OTP ─────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="mt-8 space-y-6">

    
        {{-- ── OTP WhatsApp ───────────────────────────────────────────── --}}
        @if ($user->phone && !$user->hasVerifiedPhone())
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                <h3 class="mb-1 text-sm font-semibold text-emerald-800">📱 Verifikasi Nomor WhatsApp</h3>
                <p class="mb-3 text-xs text-emerald-600">
                    Kirim kode OTP ke nomor <strong>{{ $user->phone }}</strong> via WhatsApp, lalu masukkan kodenya.
                </p>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="button" @click="sendPhoneOtp()" :disabled="phoneCooldown > 0 || phoneLoading"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:opacity-50">
                        <span x-show="!phoneLoading">
                            <span x-show="phoneCooldown === 0">Kirim OTP ke WhatsApp</span>
                            <span x-show="phoneCooldown > 0">Kirim ulang (<span x-text="phoneCooldown"></span>d)</span>
                        </span>
                        <span x-show="phoneLoading">Mengirim...</span>
                    </button>
                </div>

                <template x-if="phoneSent">
                    <div class="mt-3">
                        <p class="mb-2 text-xs text-emerald-700">Masukkan 6-digit kode OTP dari WhatsApp:</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="phoneOtp" maxlength="6" inputmode="numeric"
                                placeholder="_ _ _ _ _ _"
                                class="w-36 rounded-lg border border-emerald-300 px-3 py-2 text-center text-lg font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                            <button type="button" @click="verifyPhoneOtp()"
                                :disabled="phoneOtp.length !== 6 || phoneVerifying"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700 disabled:opacity-50">
                                <span x-show="!phoneVerifying">Verifikasi</span>
                                <span x-show="phoneVerifying">Memeriksa...</span>
                            </button>
                        </div>
                        <p x-show="phoneMsg" x-text="phoneMsg"
                            :class="phoneSuccess ? 'text-green-600' : 'text-red-500'" class="mt-1 text-xs font-medium">
                        </p>
                    </div>
                </template>
            </div>
        @elseif (!$user->phone)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                ⚠️ Isi nomor WhatsApp di form profil di atas dan simpan terlebih dahulu sebelum verifikasi WA.
            </div>
        @else
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                ✅ <strong>Nomor WhatsApp terverifikasi.</strong> Tidak perlu melakukan verifikasi ulang.
            </div>
        @endif

    </div>{{-- end .space-y-6 --}}
</section>

<script>
    function profileOtp() {
        return {
            // Phone OTP state
            phoneSent: false,
            phoneLoading: false,
            phoneVerifying: false,
            phoneOtp: '',
            phoneMsg: '',
            phoneSuccess: false,
            phoneCooldown: 0,
            phoneTimer: null,

            // ── Phone ─────────────────────────────────────────────────────
            async sendPhoneOtp() {
                this.phoneLoading = true;
                this.phoneMsg = '';
                try {
                    const res = await fetch('{{ route('otp.phone.send') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                    });
                    const data = await res.json();
                    this.phoneMsg = data.message;
                    this.phoneSuccess = res.ok;
                    if (res.ok) {
                        this.phoneSent = true;
                        this.startCooldown('phone', 60);
                    }
                } catch (e) {
                    this.phoneMsg = 'Terjadi kesalahan. Coba lagi.';
                }
                this.phoneLoading = false;
            },

            async verifyPhoneOtp() {
                this.phoneVerifying = true;
                this.phoneMsg = '';
                try {
                    const res = await fetch('{{ route('otp.phone.verify') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            otp: this.phoneOtp
                        }),
                    });
                    const data = await res.json();
                    this.phoneMsg = data.message;
                    this.phoneSuccess = res.ok;
                    if (res.ok) {
                        setTimeout(() => window.location.reload(), 1200);
                    }
                } catch (e) {
                    this.phoneMsg = 'Terjadi kesalahan. Coba lagi.';
                }
                this.phoneVerifying = false;
            },

            // ── Cooldown helper ───────────────────────────────────────────
            startCooldown(type, seconds) {
                const key = type + 'Cooldown';
                const timerKey = type + 'Timer';
                this[key] = seconds;
                clearInterval(this[timerKey]);
                this[timerKey] = setInterval(() => {
                    this[key]--;
                    if (this[key] <= 0) {
                        this[key] = 0;
                        clearInterval(this[timerKey]);
                    }
                }, 1000);
            },
        };
    }
</script>
