<?php

namespace App\Http\Controllers;

use App\DTOs\SearchDTO;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class StockMovementsController extends Controller
{
    /**
     * Display the stock movements index page
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('stock-movements/Index');
    }

    /**
     * Find stock movements by filters
     */
    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $query = StockMovement::with(['product', 'user']);

            // Apply filters
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->input('product_id'));
            }

            if ($request->filled('movement_type')) {
                $query->where('movement_type', $request->input('movement_type'));
            }

            if ($request->filled('reason')) {
                $query->where('reason', $request->input('reason'));
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            // Apply search
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->input('per_page', 10);
            $movements = $query->paginate($perPage);

            return response()->json([
                'movements' => [
                    'items' => StockMovementResource::collection($movements->items()),
                    'total_items' => $movements->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error searching stock movements', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar movimentações',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get stock movements for a specific product
     */
    public function getByProduct(string $productId): JsonResponse
    {
        try {
            $movements = StockMovement::with(['product', 'user'])
                ->where('product_id', $productId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'movements' => StockMovementResource::collection($movements),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching product movements', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar movimentações do produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
