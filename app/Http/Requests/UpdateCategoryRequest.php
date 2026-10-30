<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        /** @var Category|null $category */
        $category = $this->route('category');

        return [
            'kode_kategori' => ['required', 'string', 'max:30', Rule::unique('categories', 'kode_kategori')->ignore($category?->id)],
            'nama_kategori' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan oleh kategori lain.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
        ];
    }
}
