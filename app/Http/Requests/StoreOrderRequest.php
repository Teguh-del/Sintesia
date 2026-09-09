<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_method' => ['required', 'string', 'in:Ambil di Lokasi Petani,Pengiriman / Kurir'],
            'payment_method' => ['required', 'string', 'in:Transfer Bank / Rekber SINTESA,Tunai / COD saat Timbang'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Kuantitas pesanan wajib diisi.',
            'quantity.min' => 'Kuantitas pesanan minimal harus lebih dari 0.',
            'shipping_address.required' => 'Alamat pengiriman / domisili pembeli wajib diisi.',
            'shipping_method.required' => 'Pilih salah satu metode pengiriman.',
            'payment_method.required' => 'Pilih salah satu metode pembayaran.',
        ];
    }
}
