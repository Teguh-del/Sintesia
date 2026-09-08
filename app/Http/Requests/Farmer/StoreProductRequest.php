<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'commodity_id' => ['required', 'exists:commodities,id'],
            'stock_id' => ['nullable', 'exists:stocks,id'],
            'price' => ['required', 'numeric', 'min:100'],
            'stock' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'min_order' => ['required', 'numeric', 'min:0.01'],
            'quality' => ['required', 'string', 'max:100'],
            'harvest_date' => ['nullable', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:active,inactive,sold_out'],
            'allow_negotiation' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'commodity_id.required' => 'Pilihan komoditas wajib dipilih.',
            'commodity_id.exists' => 'Komoditas yang dipilih tidak valid.',
            'price.required' => 'Harga per satuan wajib diisi.',
            'price.min' => 'Harga minimal adalah Rp 100.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'min_order.required' => 'Minimal pembelian wajib diisi.',
            'quality.required' => 'Kualitas produk wajib dipilih/diisi.',
            'location.required' => 'Lokasi kebun/panen wajib diisi.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'images.*.max' => 'Ukuran gambar maksimal adalah 3 MB.',
        ];
    }
}
