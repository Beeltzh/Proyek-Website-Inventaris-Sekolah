<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'kondisi_saat_kembali' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'catatan_pengembalian' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'kondisi_saat_kembali.required' => 'Kondisi fisik barang saat dikembalikan wajib dipilih.',
        ];
    }
}
