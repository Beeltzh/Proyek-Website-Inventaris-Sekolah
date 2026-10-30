<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'catatan_admin' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_admin.required' => 'Alasan penolakan peminjaman wajib diisi.',
        ];
    }
}
