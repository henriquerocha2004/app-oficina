<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\Admin\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function __construct(
        private AdminAuthService $authService
    ) {
    }

    public function showLoginForm(): Response
    {
        return Inertia::render('Admin/Login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        try {
            $loginDTO = $request->toDTO();
            $result = $this->authService->login($loginDTO);

            if ($result->isFailed()) {
                return back()->withErrors([
                    'email' => $result->errorMessage,
                ])->onlyInput('email');
            }

            $this->authService->regenerateSession($request);

            return redirect()->intended(route('admin.dashboard'));
        } catch (\Throwable $e) {
            Log::error('Erro durante login do admin', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            $this->authService->logout($request);

            return redirect()->route('admin.login');
        } catch (\Throwable $e) {
            Log::error('Erro durante logout do admin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.login');
        }
    }
}
