<?php

namespace App\Services\Admin;

use App\DTOs\Admin\AdminAuthResultDTO;
use App\DTOs\Admin\AdminLoginDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    public function login(AdminLoginDTO $loginDTO): AdminAuthResultDTO
    {
        $credentials = $loginDTO->credentials();

        if (!Auth::guard('admin')->attempt($credentials, $loginDTO->remember)) {
            return AdminAuthResultDTO::failed(
                'As credenciais fornecidas não correspondem aos nossos registros.'
            );
        }

        $user = Auth::guard('admin')->user();

        return AdminAuthResultDTO::success($user);
    }

    public function regenerateSession(Request $request): void
    {
        $request->session()->regenerate();
    }

    public function logout(Request $request): void
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function isAuthenticated(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function user()
    {
        return Auth::guard('admin')->user();
    }
}
