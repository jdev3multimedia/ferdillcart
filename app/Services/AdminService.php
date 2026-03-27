<?php

namespace App\Services;

use App\Contracts\AdminServiceInterface;

class AdminService implements AdminServiceInterface
{
    public function isLoginSuccessful(string $email, string $password, string|null|bool $rememberToken): bool
    {
        if (auth('admin')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        auth()->guard('web')->logout();
        session()->invalidate();
    }
}
