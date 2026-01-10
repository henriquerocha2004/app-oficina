<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SubscriptionPlanService;
use App\DTOs\Admin\SubscriptionPlanInputDTO;
use App\DTOs\SearchDTO;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AdminPlansController extends Controller
{
    public function __construct(
        private readonly SubscriptionPlanService $planService
    ) {}

    public function index(): InertiaResponse
    {
        $searchDTO = new SearchDTO();
        $plans = $this->planService->list($searchDTO);

        return Inertia::render('admin/plans/Index', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_users' => 'required|integer|min:1',
            'max_clients' => 'required|integer|min:1',
            'max_vehicles' => 'required|integer|min:1',
            'max_services_per_month' => 'required|integer|min:1',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $dto = SubscriptionPlanInputDTO::fromRequest($validated);
        $this->planService->create($dto);

        return redirect()->back()->with('success', 'Plan created successfully.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_users' => 'integer|min:1',
            'max_clients' => 'integer|min:1',
            'max_vehicles' => 'integer|min:1',
            'max_services_per_month' => 'integer|min:1',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $dto = SubscriptionPlanInputDTO::fromRequest($validated);
        $this->planService->update($id, $dto);

        return redirect()->back()->with('success', 'Plan updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->planService->delete($id);

        return redirect()->back()->with('success', 'Plan deleted successfully.');
    }

    public function list(Request $request): JsonResponse
    {
        $searchDTO = new SearchDTO(
            search: $request->query('search'),
            per_page: (int) $request->query('per_page', 10),
            sort_by: $request->query('sort_by', 'created_at'),
            sort_direction: $request->query('sort_direction', 'desc')
        );

        $result = $this->planService->list($searchDTO);

        return response()->json([
            'plans' => [
                'items' => $result->items(),
                'total_items' => $result->total(),
            ],
        ]);
    }
}
