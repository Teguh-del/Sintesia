<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['petani', 'pengepul', 'konsumen'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            // Conditional profile fields
            'farm_name' => ['nullable', 'string', 'max:255'],
            'farm_area_hectares' => ['nullable', 'numeric', 'min:0'],
            'primary_commodity' => ['nullable', 'string', 'max:100'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role yang dipilih tidak valid. Pilihan yang diizinkan hanya Petani, Pengepul, atau Konsumen.',
            'email.unique' => 'Alamat email sudah terdaftar dalam sistem.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
        ];
    }
}
