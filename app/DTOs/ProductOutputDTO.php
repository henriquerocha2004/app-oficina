<?php

namespace App\DTOs;

use App\Models\Product;

readonly class ProductOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public float $unit_price,
        public string $category,
        public int $stock_quantity,
        public string $unit,
        public bool $is_active,
        public ?string $description = null,
        public ?string $sku = null,
        public ?string $barcode = null,
        public ?string $manufacturer = null,
        public ?int $min_stock_level = null,
        public ?float $suggested_price = null,
        public bool $is_low_stock = false,
        public ?array $suppliers = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    public static function fromModel(Product $product): self
    {
        $suppliers = null;
        if ($product->relationLoaded('suppliers')) {
            $suppliers = $product->suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'supplier_sku' => $supplier->pivot->supplier_sku,
                    'cost_price' => (float) $supplier->pivot->cost_price,
                    'lead_time_days' => $supplier->pivot->lead_time_days,
                    'min_order_quantity' => $supplier->pivot->min_order_quantity,
                    'is_preferred' => $supplier->pivot->is_preferred,
                    'notes' => $supplier->pivot->notes,
                ];
            })->toArray();
        }

        return new self(
            id: $product->id,
            name: $product->name,
            unit_price: (float) $product->unit_price,
            category: $product->category,
            stock_quantity: $product->stock_quantity,
            unit: $product->unit,
            is_active: $product->is_active,
            description: $product->description,
            sku: $product->sku,
            barcode: $product->barcode,
            manufacturer: $product->manufacturer,
            min_stock_level: $product->min_stock_level,
            suggested_price: $product->suggested_price ? (float) $product->suggested_price : null,
            is_low_stock: $product->is_low_stock,
            suppliers: $suppliers,
            created_at: $product->created_at?->toIso8601String(),
            updated_at: $product->updated_at?->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'manufacturer' => $this->manufacturer,
            'category' => $this->category,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_level' => $this->min_stock_level,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'suggested_price' => $this->suggested_price,
            'is_active' => $this->is_active,
            'is_low_stock' => $this->is_low_stock,
            'suppliers' => $this->suppliers,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }
}
