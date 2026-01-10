<?php

namespace App\DTOs;

readonly class ProductInputDTO
{
    public function __construct(
        public string $name,
        public float $unit_price,
        public string $category,
        public ?string $description = null,
        public ?string $sku = null,
        public ?string $barcode = null,
        public ?string $manufacturer = null,
        public int $stock_quantity = 0,
        public ?int $min_stock_level = null,
        public string $unit = 'unit',
        public ?float $suggested_price = null,
        public bool $is_active = true,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            unit_price: (float) $data['unit_price'],
            category: $data['category'],
            description: $data['description'] ?? null,
            sku: $data['sku'] ?? null,
            barcode: $data['barcode'] ?? null,
            manufacturer: $data['manufacturer'] ?? null,
            stock_quantity: isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : 0,
            min_stock_level: isset($data['min_stock_level']) && $data['min_stock_level'] !== ''
                ? (int) $data['min_stock_level']
                : null,
            unit: $data['unit'] ?? 'unit',
            suggested_price: isset($data['suggested_price']) && $data['suggested_price'] !== ''
                ? (float) $data['suggested_price']
                : null,
            is_active: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
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
        ], fn($value) => $value !== null);
    }
}
