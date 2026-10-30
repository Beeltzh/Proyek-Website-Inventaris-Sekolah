<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $isAdmin = (bool) $this->user()?->isAdmin();

        return [
            'item_id' => ['required', 'exists:items,id'],
            'user_id' => [$isAdmin ? 'required' : 'nullable', 'exists:users,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'rencana_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang inventaris yang dipinjam wajib dipilih.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'user_id.required' => 'Peminjam wajib dipilih.',
            'jumlah.required' => 'Jumlah unit yang dipinjam wajib diisi.',
            'jumlah.min' => 'Jumlah peminjaman minimal 1 unit.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'rencana_kembali.required' => 'Rencana tanggal kembali wajib diisi.',
            'rencana_kembali.after_or_equal' => 'Rencana tanggal kembali tidak boleh lebih awal dari tanggal pinjam.',
            'keperluan.required' => 'Keperluan peminjaman wajib diisi.',
        ];
    }
}
