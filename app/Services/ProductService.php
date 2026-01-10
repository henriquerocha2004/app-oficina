<?php

namespace App\Services;

use App\DTOs\ProductInputDTO;
use App\DTOs\ProductOutputDTO;
use App\DTOs\SearchDTO;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    use QueryBuilderTrait;

    public function create(ProductInputDTO $dto): ProductOutputDTO
    {
        $product = Product::create($dto->toArray());
        $product->load('suppliers');

        return ProductOutputDTO::fromModel($product);
    }

    public function update(string $id, ProductInputDTO $dto): void
    {
        $product = Product::findOrFail($id);
        $product->update($dto->toArray());
    }

    public function delete(string $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function find(string $id): ?ProductOutputDTO
    {
        $product = Product::with('suppliers')->find($id);

        if (!$product) {
            return null;
        }

        return ProductOutputDTO::fromModel($product);
    }

    public function findById(string $id): ?Product
    {
        return Product::with('suppliers')->find($id);
    }

    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Product::with('suppliers');

        $searchableColumns = ['name', 'sku', 'barcode', 'manufacturer', 'category'];

        return $this->applySearchAndFilters($query, $searchDTO, $searchableColumns);
    }

    /**
     * Adjust product stock
     *
     * @throws \Exception if product not found or insufficient stock
     */
    public function adjustStock(
        string $productId,
        int $quantity,
        string $movementType,
        string $reason,
        ?string $notes = null,
        ?string $referenceType = null,
        ?string $referenceId = null
    ): StockMovement {
        $product = Product::findOrFail($productId);

        // Validate outbound movement
        if ($movementType === StockMovement::TYPE_OUT && $product->stock_quantity < $quantity) {
            throw new InsufficientStockException(
                available: $product->stock_quantity,
                requested: $quantity
            );
        }

        return DB::transaction(function () use (
            $product,
            $quantity,
            $movementType,
            $reason,
            $notes,
            $referenceType,
            $referenceId
        ) {
            // Update stock (cache)
            if ($movementType === StockMovement::TYPE_IN) {
                $product->increment('stock_quantity', $quantity);
            } else {
                $product->decrement('stock_quantity', $quantity);
            }

            $product->refresh();

            // Create movement record (audit trail)
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'balance_after' => $product->stock_quantity,
                'reason' => $reason,
                'notes' => $notes,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => auth()->id(),
            ]);

            Log::info('Stock adjusted', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'balance_after' => $product->stock_quantity,
                'reason' => $reason,
                'user_id' => auth()->id(),
            ]);

            return $movement;
        });
    }

    /**
     * Recalculate stock from movement history
     * Used when stock discrepancy is detected
     */
    public function recalculateStock(string $productId): int
    {
        $product = Product::findOrFail($productId);

        $calculatedStock = StockMovement::where('product_id', $productId)
            ->selectRaw('
                SUM(CASE WHEN movement_type = ? THEN quantity ELSE 0 END) -
                SUM(CASE WHEN movement_type = ? THEN quantity ELSE 0 END) as total
            ', [StockMovement::TYPE_IN, StockMovement::TYPE_OUT])
            ->value('total') ?? 0;

        $oldStock = $product->stock_quantity;
        $product->update(['stock_quantity' => $calculatedStock]);

        Log::warning('Stock recalculated', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'old_stock' => $oldStock,
            'new_stock' => $calculatedStock,
            'difference' => $calculatedStock - $oldStock,
        ]);

        return $calculatedStock;
    }

    public function getLowStockProducts(): Collection
    {
        return Product::lowStock()
            ->active()
            ->with('suppliers')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    public function getActiveProducts(): Collection
    {
        return Product::active()
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'sku']);
    }
}
