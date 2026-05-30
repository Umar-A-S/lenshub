<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi: terima field 'identifier' (email / username / nomor WA)
     * dan 'password'.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Email, username, atau nomor WA wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ];
    }

    /**
     * Coba autentikasi dengan email, username, atau nomor WA.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim($this->input('identifier'));
        $password   = $this->input('password');

        // Cari user berdasarkan email, username, atau nomor WA
        $user = $this->findUser($identifier);

        if (! $user || ! Auth::attempt(['email' => $user->email, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Cari user berdasarkan email / username / nomor WA.
     */
    private function findUser(string $identifier): ?User
    {
        // Coba email dulu
        $user = User::where('email', $identifier)->first();
        if ($user) return $user;

        // Coba username
        $user = User::where('username', $identifier)->first();
        if ($user) return $user;

        // Coba nomor WA (normalisasi: 08xxx atau 628xxx)
        $normalized = $this->normalizePhone($identifier);
        $user = User::where('phone', $normalized)
            ->orWhere('phone', $identifier)
            ->first();

        return $user;
    }

    /**
     * Normalisasi nomor WA ke format penyimpanan (misal 08xxx).
     */
    private function normalizePhone(string $nomor): string
    {
        $clean = preg_replace('/[^0-9]/', '', $nomor);
        // Ubah 628xxx → 08xxx agar cocok dengan data yang disimpan
        if (str_starts_with($clean, '62')) {
            return '0' . substr($clean, 2);
        }
        return $clean;
    }

    /**
     * Pastikan request tidak di-rate-limit.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identifier')) . '|' . $this->ip());
    }
}
