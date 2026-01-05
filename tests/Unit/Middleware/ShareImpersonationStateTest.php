<?php

use App\Http\Middleware\ShareImpersonationState;
use App\Services\Admin\ImpersonationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

beforeEach(function () {
    $this->middleware = new ShareImpersonationState();
    $this->impersonationService = Mockery::mock(ImpersonationService::class);
    app()->instance(ImpersonationService::class, $this->impersonationService);
});

it('shares impersonation state when impersonating', function () {
    $this->impersonationService
        ->shouldReceive('isImpersonating')
        ->once()
        ->andReturn(true);
    
    $this->impersonationService
        ->shouldReceive('getImpersonationData')
        ->once()
        ->andReturn([
            'admin_id' => 'admin-123',
            'admin_name' => 'Super Admin',
            'tenant_id' => 'tenant-123',
            'user_id' => 'user-123',
        ]);
    
    $request = Request::create('/test', 'GET');
    
    $this->middleware->handle($request, function ($req) {
        // Request passed through
        return response('OK');
    });
    
    // Check if Inertia shared the data
    $shared = Inertia::getShared();
    expect($shared)->toHaveKey('impersonating')
        ->and($shared['impersonating'])->toBeTrue()
        ->and($shared)->toHaveKey('impersonationData');
});

it('does not share when not impersonating', function () {
    $this->impersonationService
        ->shouldReceive('isImpersonating')
        ->once()
        ->andReturn(false);
    
    $request = Request::create('/test', 'GET');
    
    $this->middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    $shared = Inertia::getShared();
    expect($shared['impersonating'] ?? false)->toBeFalse();
});
