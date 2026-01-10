<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\SearchDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use App\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use App\Services\Admin\SubscriptionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionPlansController extends Controller
{
    public function __construct(
        private SubscriptionPlanService $planService
    ) {
    }

    public function index(Request $request): Response
    {
        $searchDTO = SearchDTO::fromRequest($request);
        $plans = $this->planService->list($searchDTO);

        return Inertia::render('admin/plans/Index', [
            'plans' => [
                'items' => $plans->items(),
                'total_items' => $plans->total(),
            ],
        ]);
    }

    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $plans = $this->planService->list($searchDTO);

            return response()->json([
                'plans' => [
                    'items' => $plans->items(),
                    'total_items' => $plans->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar planos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erro ao carregar planos.',
            ], 500);
        }
    }

    public function create()
    {
        return redirect()->route('admin.plans.index');
    }

    public function store(StoreSubscriptionPlanRequest $request): RedirectResponse
    {
        try {
            $planDTO = $request->toDTO();
            $this->planService->create($planDTO);

            return redirect()->route('admin.plans.index')->with('success', 'Plano criado com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao criar plano de assinatura', [
                'data' => $request->validated(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao criar plano. Por favor, tente novamente.',
            ])->withInput();
        }
    }

    public function update(string $id, UpdateSubscriptionPlanRequest $request): RedirectResponse
    {
        try {
            $planDTO = $request->toDTO();
            $this->planService->update($id, $planDTO);

            return redirect()->route('admin.plans.index')->with('success', 'Plano atualizado com sucesso!');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar plano de assinatura', [
                'plan_id' => $id,
                'data' => $request->validated(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar plano. Por favor, tente novamente.',
            ])->withInput();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->planService->delete($id);

            return redirect()->route('admin.plans.index')->with('success', 'Plano excluído com sucesso!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\DomainException $e) {
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir plano de assinatura', [
                'plan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao excluir plano. Por favor, tente novamente.',
            ]);
        }
    }
}
