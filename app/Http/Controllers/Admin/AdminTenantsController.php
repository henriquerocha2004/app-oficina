<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\SearchDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;
use App\Services\Admin\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminTenantsController extends Controller
{
    public function __construct(
        private TenantService $tenantService
    ) {
    }

    public function index(Request $request): Response
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $tenants = $this->tenantService->list($searchDTO);
            $plans = $this->tenantService->getActiveSubscriptionPlans();

            return Inertia::render('admin/tenants/Index', [
                'tenants' => [
                    'items' => $tenants->items(),
                    'total_items' => $tenants->total(),
                ],
                'subscription_plans' => $plans,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao carregar página de tenants', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('admin/tenants/Index', [
                'tenants' => ['items' => [], 'total_items' => 0],
                'subscription_plans' => [],
                'error' => 'Erro ao carregar dados.',
            ]);
        }
    }

    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $tenants = $this->tenantService->list($searchDTO);

            return response()->json([
                'tenants' => [
                    'items' => $tenants->items(),
                    'total_items' => $tenants->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar tenants', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erro ao carregar oficinas.',
            ], 500);
        }
    }

    public function create()
    {
        return redirect()->route('admin.tenants.index');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        try {
            $tenantDTO = $request->toDTO();
            $this->tenantService->create($tenantDTO);

            return redirect()->route('admin.tenants.index')->with('success', 'Oficina criada com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao criar tenant', [
                'data' => $request->safe()->except(['admin_password']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao criar oficina. Por favor, tente novamente.',
            ])->withInput();
        }
    }

    public function edit(string $id)
    {
        return redirect()->route('admin.tenants.index');
    }

    public function update(string $id, UpdateTenantRequest $request): RedirectResponse
    {
        try {
            $tenantDTO = $request->toDTO();
            $this->tenantService->update($id, $tenantDTO);

            return redirect()->route('admin.tenants.index')->with('success', 'Oficina atualizada com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar tenant', [
                'tenant_id' => $id,
                'data' => $request->validated(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar oficina. Por favor, tente novamente.',
            ])->withInput();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->tenantService->delete($id);

            return redirect()->route('admin.tenants.index')->with('success', 'Oficina excluída com sucesso!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir tenant', [
                'tenant_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao excluir oficina. Por favor, tente novamente.',
            ]);
        }
    }
}
