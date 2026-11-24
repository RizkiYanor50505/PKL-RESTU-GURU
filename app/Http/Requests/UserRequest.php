<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(Role::values())],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(UserStatus::values())],
            // Password hanya wajib saat membuat akun; reset dilakukan lewat form terpisah.
            'password' => $user ? ['prohibited'] : ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
