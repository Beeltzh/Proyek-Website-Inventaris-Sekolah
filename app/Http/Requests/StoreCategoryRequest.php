<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kode_kategori')) {
            $this->merge([
                'kode_kategori' => strtoupper(trim((string) $this->input('kode_kategori'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'kode_kategori' => ['required', 'string', 'max:30', 'unique:categories,kode_kategori'],
            'nama_kategori' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori sudah terdaftar di sistem.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
        ];
    }
}
