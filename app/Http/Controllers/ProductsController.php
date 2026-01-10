<?php

namespace App\Http\Controllers;

use App\DTOs\ProductInputDTO;
use App\DTOs\SearchDTO;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\AttachSupplierToProductRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductSupplierRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Str;
use App\Models\StockMovement;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display the products index page
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('products/Index');
    }

    /**
     * Store a new product
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $dto = ProductInputDTO::fromArray($request->validated());
            $product = $this->productService->create($dto);

            return response()->json([
                'message' => 'Produto criado com sucesso',
                'product_id' => $product->id,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao criar produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing product
     */
    public function update(string $id, UpdateProductRequest $request): JsonResponse
    {
        try {
            $dto = ProductInputDTO::fromArray($request->validated());
            $this->productService->update($id, $dto);

            return response()->json([
                'message' => 'Produto atualizado com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a product
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $this->productService->delete($id);

            return response()->json([
                'message' => 'Produto excluído com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao excluir produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single product by ID
     */
    public function showById(string $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'product' => $product->toArray(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Find products by filters
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

            $products = $this->productService->list($searchDTO);

            return response()->json([
                'products' => [
                    'items' => ProductResource::collection($products->items()),
                    'total_items' => $products->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error searching products', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar produtos',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Adjust product stock
     */
    public function adjustStock(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'movement_type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'in:' . implode(',', StockMovement::getReasons())],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $movement = $this->productService->adjustStock(
                productId: $id,
                quantity: $request->input('quantity'),
                movementType: $request->input('movement_type'),
                reason: $request->input('reason'),
                notes: $request->input('notes'),
            );

            return response()->json([
                'message' => 'Estoque ajustado com sucesso',
                'movement_id' => $movement->id,
                'balance_after' => $movement->balance_after,
            ], Response::HTTP_OK);
        } catch (InsufficientStockException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [
                    'quantity' => [$e->getMessage()],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error adjusting stock', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products with low stock
     */
    public function getLowStock(): JsonResponse
    {
        try {
            $products = $this->productService->getLowStockProducts();

            return response()->json([
                'products' => ProductResource::collection($products),
                'count' => $products->count(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching low stock products', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar produtos com estoque baixo',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all active products (for select/combobox)
     */
    public function getActiveProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getActiveProducts();

            return response()->json([
                'products' => $products->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                ]),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching active products', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar produtos ativos',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get suppliers for a product
     */
    public function getSuppliers(string $id): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            $suppliers = $product->suppliers()->get()->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'document_number' => $supplier->document_number,
                    'supplier_sku' => $supplier->pivot->supplier_sku,
                    'cost_price' => (float) $supplier->pivot->cost_price,
                    'lead_time_days' => $supplier->pivot->lead_time_days,
                    'min_order_quantity' => $supplier->pivot->min_order_quantity,
                    'is_preferred' => (bool) $supplier->pivot->is_preferred,
                    'notes' => $supplier->pivot->notes,
                ];
            });

            return response()->json([
                'suppliers' => $suppliers,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching product suppliers', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar fornecedores do produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Attach supplier to product
     */
    public function attachSupplier(string $id, AttachSupplierToProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if already attached
            if ($product->suppliers()->where('supplier_id', $request->supplier_id)->exists()) {
                return response()->json([
                    'message' => 'Fornecedor já está vinculado a este produto',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product->suppliers()->attach($request->supplier_id, [
                'id' => Str::ulid()->toString(),
                'supplier_sku' => $request->supplier_sku,
                'cost_price' => $request->cost_price,
                'lead_time_days' => $request->lead_time_days,
                'min_order_quantity' => $request->min_order_quantity ?? 1,
                'is_preferred' => $request->is_preferred ?? false,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Fornecedor vinculado com sucesso',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error attaching supplier to product', [
                'product_id' => $id,
                'supplier_id' => $request->supplier_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao vincular fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update product supplier relationship
     */
    public function updateSupplier(string $productId, string $supplierId, UpdateProductSupplierRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->findById($productId);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            if (!$product->suppliers()->where('supplier_id', $supplierId)->exists()) {
                return response()->json([
                    'message' => 'Fornecedor não está vinculado a este produto',
                ], Response::HTTP_NOT_FOUND);
            }

            $product->suppliers()->updateExistingPivot($supplierId, [
                'supplier_sku' => $request->supplier_sku,
                'cost_price' => $request->cost_price,
                'lead_time_days' => $request->lead_time_days,
                'min_order_quantity' => $request->min_order_quantity ?? 1,
                'is_preferred' => $request->is_preferred ?? false,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Fornecedor atualizado com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating product supplier', [
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Detach supplier from product
     */
    public function detachSupplier(string $productId, string $supplierId): JsonResponse
    {
        try {
            $product = $this->productService->findById($productId);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            $product->suppliers()->detach($supplierId);

            return response()->json([
                'message' => 'Fornecedor removido com sucesso',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error detaching supplier from product', [
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao remover fornecedor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
