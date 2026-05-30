@extends('layouts.user')

@section('content')

<div class="bg-white rounded-3xl p-8 shadow-sm" x-data="profileOtp()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-gray-500 mt-2">Kelola informasi profil Anda.</p>
    </div>

    {{-- ── Alert sukses simpan ──────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-green-100 px-5 py-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Alert profil belum lengkap (redirect dari halaman sewa) ──── --}}
    @if(session('profil_tidak_lengkap'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
            <p class="font-semibold text-amber-800 mb-1">⚠️ Profil belum lengkap — selesaikan sebelum memesan:</p>
            <ul class="list-disc list-inside text-sm text-amber-700 space-y-1">
                @foreach(session('profil_tidak_lengkap') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-8">

        {{-- ── Kolom Kiri: Form Profil ──────────────────────────────── --}}
        <div class="col-span-2 space-y-8">

            {{-- Form data profil --}}
            <form action="{{ route('akun.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- USERNAME --}}
                <div>
                    <label class="block mb-2 font-medium">
                        Username
                        @if($user->username_changed)
                            <span class="ml-2 text-xs font-normal text-amber-600">🔒 Terkunci (sudah pernah diubah)</span>
                        @else
                            <span class="ml-2 text-xs font-normal text-gray-400">Hanya bisa diubah 1 kali</span>
                        @endif
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">@</span>
                        <input
                            type="text"
                            name="username"
                            value="{{ old('username', $user->username) }}"
                            {{ $user->username_changed ? 'readonly' : '' }}
                            placeholder="contoh: budi.santoso"
                            class="w-full rounded-xl border px-4 py-3 pl-7 {{ $user->username_changed ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}"
                        >
                    </div>
                    @error('username')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NAMA --}}
                <div>
                    <label class="block mb-2 font-medium">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full rounded-xl border px-4 py-3">
                    @error('name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block mb-2 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full rounded-xl border px-4 py-3">
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">📧 Email digunakan sebagai opsi login dan penerima rekap sewa otomatis.</p>
                </div>

                {{-- NOMOR WA --}}
                <div>
                    <label class="block mb-2 font-medium">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full rounded-xl border px-4 py-3">
                    @error('phone')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @if($user->phone && $user->hasVerifiedPhone())
                        <p class="text-xs text-green-600 mt-1">✅ Nomor WA sudah diverifikasi</p>
                    @elseif($user->phone)
                        <p class="text-xs text-red-500 mt-1">⚠️ Nomor WA belum diverifikasi — wajib sebelum memesan</p>
                    @endif
                </div>

                {{-- FOTO --}}
                <div>
                    <label class="block mb-2 font-medium">Upload Foto</label>
                    <input type="file" name="photo" class="w-full rounded-xl border px-4 py-3 bg-white">
                    @error('photo')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="rounded-xl bg-[#073090] px-6 py-3 text-white font-semibold hover:bg-blue-900 transition">
                    Simpan Profil
                </button>
            </form>



            {{-- ── Panel OTP WhatsApp ───────────────────────────────── --}}
            @if(!$user->phone)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                ⚠️ Isi nomor WhatsApp di form atas lalu <strong>Simpan Profil</strong> terlebih dahulu sebelum bisa verifikasi WA.
            </div>
            @elseif(!$user->hasVerifiedPhone())
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <h3 class="font-semibold text-emerald-800 mb-1">📱 Verifikasi WhatsApp</h3>
                <p class="text-sm text-emerald-600 mb-4">
                    Kirim kode OTP ke <strong>{{ $user->phone }}</strong> via WhatsApp.
                </p>

                <button type="button" @click="sendPhoneOtp()"
                    :disabled="phoneCooldown > 0 || phoneLoading"
                    class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50 transition">
                    <span x-show="!phoneLoading">
                        <span x-show="phoneCooldown === 0">Kirim OTP ke WhatsApp</span>
                        <span x-show="phoneCooldown > 0">Kirim ulang (<span x-text="phoneCooldown"></span>d)</span>
                    </span>
                    <span x-show="phoneLoading">Mengirim...</span>
                </button>

                <template x-if="phoneSent">
                    <div class="mt-4 space-y-2">
                        <p class="text-sm text-emerald-700">Masukkan 6-digit kode dari WhatsApp:</p>
                        <div class="flex gap-3">
                            <input type="text" x-model="phoneOtp" maxlength="6" inputmode="numeric"
                                placeholder="_ _ _ _ _ _"
                                class="w-36 rounded-xl border border-emerald-300 px-3 py-2 text-center text-xl font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <button type="button" @click="verifyPhoneOtp()"
                                :disabled="phoneOtp.length !== 6 || phoneVerifying"
                                class="rounded-xl bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-50 transition">
                                <span x-show="!phoneVerifying">Verifikasi</span>
                                <span x-show="phoneVerifying">Memeriksa...</span>
                            </button>
                        </div>
                        <p x-show="phoneMsg" x-text="phoneMsg"
                            :class="phoneSuccess ? 'text-green-600' : 'text-red-500'"
                            class="text-sm font-medium"></p>
                    </div>
                </template>
            </div>
            @else
            <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                ✅ <strong>Nomor WhatsApp terverifikasi.</strong>
            </div>
            @endif

        </div>{{-- end col kiri --}}

        {{-- ── Kolom Kanan: Foto ────────────────────────────────────── --}}
        <div class="flex flex-col items-center justify-start pt-4 border-l pl-8">
            <div class="w-44 h-44 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil"
                        class="w-full h-full object-cover">
                @else
                    <span class="text-gray-400 text-6xl">👤</span>
                @endif
            </div>
            <p class="mt-4 text-gray-500 text-sm text-center">Ukuran gambar maks. 2 MB</p>

            {{-- Ringkasan status verifikasi --}}
            <div class="mt-6 w-full space-y-2 text-sm">
                <div class="flex items-center gap-2 {{ $user->username ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $user->username ? '✅' : '⬜' }} Username
                </div>
                <div class="flex items-center gap-2 {{ $user->email ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $user->email ? '✅' : '⬜' }} Email (rekap sewa)
                </div>
                <div class="flex items-center gap-2 {{ ($user->phone && $user->hasVerifiedPhone()) ? 'text-green-600' : 'text-red-500' }}">
                    {{ ($user->phone && $user->hasVerifiedPhone()) ? '✅' : '❌' }} WhatsApp
                </div>
                @if($user->profilLengkap())
                    <p class="mt-3 rounded-lg bg-green-100 px-3 py-2 text-xs text-green-700 font-semibold text-center">
                        🎉 Profil lengkap — siap memesan!
                    </p>
                @else
                    <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600 text-center">
                        Lengkapi profil untuk bisa memesan.
                    </p>
                @endif
            </div>
        </div>

    </div>{{-- end grid --}}
</div>

<script>
function profileOtp() {
    return {
        phoneSent: false, phoneLoading: false, phoneVerifying: false,
        phoneOtp: '', phoneMsg: '', phoneSuccess: false, phoneCooldown: 0, phoneTimer: null,

        async sendPhoneOtp() {
            this.phoneLoading = true; this.phoneMsg = '';
            try {
                const res  = await fetch('{{ route('otp.phone.send') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.phoneMsg = data.message; this.phoneSuccess = res.ok;
                if (res.ok) { this.phoneSent = true; this.startCooldown('phone', 60); }
            } catch(e) { this.phoneMsg = 'Terjadi kesalahan. Coba lagi.'; }
            this.phoneLoading = false;
        },

        async verifyPhoneOtp() {
            this.phoneVerifying = true; this.phoneMsg = '';
            try {
                const res  = await fetch('{{ route('otp.phone.verify') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ otp: this.phoneOtp }),
                });
                const data = await res.json();
                this.phoneMsg = data.message; this.phoneSuccess = res.ok;
                if (res.ok) setTimeout(() => window.location.reload(), 1200);
            } catch(e) { this.phoneMsg = 'Terjadi kesalahan. Coba lagi.'; }
            this.phoneVerifying = false;
        },

        startCooldown(type, seconds) {
            const key = type + 'Cooldown', tKey = type + 'Timer';
            this[key] = seconds;
            clearInterval(this[tKey]);
            this[tKey] = setInterval(() => { if (--this[key] <= 0) { this[key] = 0; clearInterval(this[tKey]); } }, 1000);
        },
    };
}
</script>

@endsection
