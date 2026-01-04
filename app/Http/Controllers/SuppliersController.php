<?php

namespace App\Http\Controllers;

use App\DTOs\SearchDTO;
use App\DTOs\SupplierInputDTO;
use App\Http\Requests\CreateSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class SuppliersController extends Controller
{
    public function __construct(
        private SupplierService $supplierService
    ) {}

    /**
     * Display the suppliers index page
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('suppliers/Index');
    }

    /**
     * Store a new supplier
     */
    public function store(CreateSupplierRequest $request): JsonResponse
    {
        try {
            $dto = SupplierInputDTO::fromArray($request->validated());
            $supplier = $this->supplierService->create($dto);

            return response()->json([
                'message' => 'Fornecedor criado com sucesso',
                'supplier_id' => $supplier->id,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating supplier', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao criar fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing supplier
     */
    public function update(string $id, UpdateSupplierRequest $request): JsonResponse
    {
        try {
            $dto = SupplierInputDTO::fromArray($request->validated());
            $this->supplierService->update($id, $dto);

            return response()->json([
                'message' => 'Fornecedor atualizado com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating supplier', [
                'supplier_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a supplier
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $this->supplierService->delete($id);

            return response()->json([
                'message' => 'Fornecedor excluído com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting supplier', [
                'supplier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao excluir fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single supplier by ID
     */
    public function showById(string $id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->find($id);

            if (!$supplier) {
                return response()->json([
                    'message' => 'Fornecedor não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'supplier' => $supplier->toArray(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier', [
                'supplier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Find suppliers by filters
     */
    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = new SearchDTO(
                search: $request->input('search', ''),
                per_page: $request->input('per_page', 10),
                sort_by: $request->input('sort_by', 'created_at'),
                sort_direction: $request->input('sort_direction', 'desc'),
                filters: $request->input('filters', []),
            );

            $suppliers = $this->supplierService->list($searchDTO);

            return response()->json([
                'suppliers' => [
                    'items' => SupplierResource::collection($suppliers->items()),
                    'total_items' => $suppliers->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error searching suppliers', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar fornecedores',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
