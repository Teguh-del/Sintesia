<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreHarvestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isPetani();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commodity_id' => ['required', 'exists:commodities,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:50'],
            'harvest_date' => ['required', 'date'],
            'quality' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'update_profile_location' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'commodity_id.required' => 'Pilihan komoditas wajib dipilih.',
            'commodity_id.exists' => 'Komoditas yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah hasil panen wajib diisi.',
            'quantity.min' => 'Jumlah hasil panen harus lebih besar dari 0.',
            'unit.required' => 'Satuan komoditas wajib diisi.',
            'harvest_date.required' => 'Tanggal panen wajib diisi.',
            'quality.required' => 'Kualitas mutu panen wajib dipilih/diisi.',
            'location.required' => 'Lokasi lahan / kebun panen wajib diisi.',
        ];
    }
}
