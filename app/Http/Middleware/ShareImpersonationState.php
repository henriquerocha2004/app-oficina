<?php

namespace App\Http\Middleware;

use App\Services\Admin\ImpersonationService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareImpersonationState
{
    public function __construct(
        private ImpersonationService $impersonationService
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->impersonationService->isImpersonating()) {
            Inertia::share([
                'impersonating' => true,
                'impersonationData' => $this->impersonationService->getImpersonationData(),
            ]);
        }

        return $next($request);
    }
}
