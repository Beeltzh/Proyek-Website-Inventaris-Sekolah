<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,user'],
            'user_type' => ['required', 'in:Pengelola,Guru,Siswa,Staf'],
            'nomor_induk' => ['nullable', 'string', 'max:50'],
            'kelas_jabatan' => ['nullable', 'string', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'password' => ['required', Password::min(6)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
            'role.required' => 'Hak akses sistem wajib dipilih.',
            'user_type.required' => 'Kategori pengguna wajib dipilih.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ];
    }
}
