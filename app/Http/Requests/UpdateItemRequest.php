<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kode_barang')) {
            $this->merge([
                'kode_barang' => strtoupper(trim((string) $this->input('kode_barang'))),
            ]);
        }
    }

    public function rules(): array
    {
        /** @var Item|null $item */
        $item = $this->route('item');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('items', 'kode_barang')->ignore($item?->id)],
            'nama_barang' => ['required', 'string', 'max:255'],
            'merk' => ['nullable', 'string', 'max:150'],
            'lokasi' => ['required', 'string', 'max:150'],
            'stok_total' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan oleh barang lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'lokasi.required' => 'Lokasi penyimpanan wajib diisi.',
            'stok_total.required' => 'Jumlah stok total wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'kondisi.required' => 'Kondisi fisik barang wajib dipilih.',
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.max' => 'Ukuran foto barang maksimal 2 MB.',
        ];
    }
}
