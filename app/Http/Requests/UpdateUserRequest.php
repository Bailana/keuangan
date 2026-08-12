<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:superadmin,admin,administrasi,viewer',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
