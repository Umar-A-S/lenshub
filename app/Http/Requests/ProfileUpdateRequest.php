<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9_.]+$/',          // huruf kecil, angka, titik, underscore
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+62|62|0)[0-9]{8,13}$/',  // format nomor Indonesia
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex'  => 'Username hanya boleh berisi huruf kecil, angka, titik (.), dan underscore (_).',
            'phone.regex'     => 'Format nomor WA tidak valid. Gunakan format 08xx, +628xx, atau 628xx.',
        ];
    }
}
