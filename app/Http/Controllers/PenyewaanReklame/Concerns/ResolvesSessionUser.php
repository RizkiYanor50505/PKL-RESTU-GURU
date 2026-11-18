<?php

namespace App\Http\Controllers\PenyewaanReklame\Concerns;

use App\Models\User;

trait ResolvesSessionUser
{
    protected function resolveSessionUserId(): ?int
    {
        $email = session('auth_user.email');

        if (! is_string($email) || $email === '') {
            return null;
        }

        return User::where('email', $email)->value('id');
    }
}
