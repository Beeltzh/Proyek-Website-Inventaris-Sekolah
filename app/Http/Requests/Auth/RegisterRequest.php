<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_type' => ['required', 'in:Guru,Siswa,Staf'],
            'nomor_induk' => ['required', 'string', 'max:50'],
            'kelas_jabatan' => ['required', 'string', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email tersebut sudah terdaftar.',
            'user_type.required' => 'Status warga sekolah wajib dipilih.',
            'nomor_induk.required' => 'Nomor induk (NISN / NIP) wajib diisi.',
            'kelas_jabatan.required' => 'Kelas atau jabatan wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ];
    }
}
